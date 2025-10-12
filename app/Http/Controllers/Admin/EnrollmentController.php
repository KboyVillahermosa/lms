<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnrollmentRequest;
use App\Models\EnrollmentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EnrollmentController extends Controller
{
    public function index()
    {
        $enrollmentRequests = EnrollmentRequest::with(['user', 'course', 'admin', 'documents.documentType'])
            ->orderBy('status', 'asc')  // pending first
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.enrollments.index', compact('enrollmentRequests'));
    }

    /**
     * Show detailed view of enrollment application
     */
    public function show(EnrollmentRequest $enrollmentRequest)
    {
        $enrollmentRequest->load(['user', 'course', 'admin', 'documents.documentType', 'documents.reviewer']);
        
        return view('admin.enrollments.show', compact('enrollmentRequest'));
    }

    /**
     * Download document for verification
     */
    public function downloadDocument(EnrollmentRequest $enrollmentRequest, EnrollmentDocument $document)
    {
        // Ensure the document belongs to the enrollment request
        if ($document->enrollment_request_id !== $enrollmentRequest->id) {
            abort(404);
        }

        // Ensure file exists
        if (!Storage::disk('private')->exists($document->file_path)) {
            // Return to admin with error and log
            Log::warning('Enrollment document missing on disk', ['document_id' => $document->id, 'path' => $document->file_path]);
            return back()->with('error', 'Document file not found on disk.');
        }

        $mime = $document->mime_type ?? Storage::disk('private')->mimeType($document->file_path);

        // For common previewable types (images and PDFs), return inline so browsers open them directly
        if (str_starts_with($mime, 'image/') || $mime === 'application/pdf') {
            $stream = Storage::disk('private')->get($document->file_path);
            $filename = addslashes($document->original_filename);
            $disposition = "inline; filename=\"{$filename}\"";
            return response($stream, 200)
                ->header('Content-Type', $mime)
                ->header('Content-Disposition', $disposition);
        }

        // Default - force download
        return Storage::disk('private')->download($document->file_path, $document->original_filename);
    }

    /**
     * Stream document inline for preview (more efficient for large files)
     */
    public function previewDocument(EnrollmentRequest $enrollmentRequest, EnrollmentDocument $document)
    {
        // Ensure the document belongs to the enrollment request
        if ($document->enrollment_request_id !== $enrollmentRequest->id) {
            abort(404);
        }

        if (!Storage::disk('private')->exists($document->file_path)) {
            Log::warning('Enrollment document missing on disk (preview)', ['document_id' => $document->id, 'path' => $document->file_path]);
            return back()->with('error', 'Document file not found on disk.');
        }

        $mime = $document->mime_type ?? Storage::disk('private')->mimeType($document->file_path);

        $stream = Storage::disk('private')->readStream($document->file_path);
        if ($stream === false) {
            Log::error('Failed to open stream for enrollment document', ['document_id' => $document->id]);
            return back()->with('error', 'Unable to open document for preview.');
        }

        $filename = addslashes($document->original_filename);
        $disposition = "inline; filename=\"{$filename}\"";

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) fclose($stream);
        }, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => $disposition,
        ]);
    }

    /**
     * Verify document (approve/reject)
     */
    public function verifyDocument(Request $request, EnrollmentRequest $enrollmentRequest, EnrollmentDocument $document)
    {
        // Ensure document belongs to enrollment
        if ($document->enrollment_request_id !== $enrollmentRequest->id) {
            abort(404);
        }
        $request->validate([
            'status' => 'required|in:approved,rejected,resubmission_required',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $document->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now()
        ]);

        // Update overall document status of enrollment request
        $this->updateEnrollmentDocumentStatus($document->enrollmentRequest);

        return back()->with('success', 'Document verification updated successfully.');
    }

    /**
     * Approve entire enrollment application
     */
    public function approve(Request $request, EnrollmentRequest $enrollmentRequest)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500'
        ]);

        // Check if all documents are approved
        $pendingDocuments = $enrollmentRequest->documents()
            ->whereIn('status', ['pending', 'rejected', 'resubmission_required'])
            ->count();

        if ($pendingDocuments > 0) {
            return back()->withErrors(['error' => 'Cannot approve enrollment until all documents are verified and approved.']);
        }

        Log::info('Admin approving enrollment', ['enrollment_request_id' => $enrollmentRequest->id, 'admin_id' => Auth::id()]);

        $enrollmentRequest->update([
            'status' => 'approved',
            'admin_id' => Auth::id(),
            'admin_notes' => $request->admin_notes,
            'processed_at' => now(),
            'document_status' => 'approved'
        ]);

        // Add the student to the course
        if ($enrollmentRequest->course) {
            $enrollmentRequest->course->students()->syncWithoutDetaching([$enrollmentRequest->user_id]);
        }

        return back()->with('success', 'Enrollment request approved successfully!');
    }

    /**
     * Reject entire enrollment application
     */
    public function reject(Request $request, EnrollmentRequest $enrollmentRequest)
    {
        // Accept a rejection reason (from the show modal) or admin_notes (from index modal)
        $request->validate([
            'reason' => 'nullable|string|max:500',
            'admin_notes' => 'nullable|string|max:500'
        ]);

        $note = $request->input('reason') ?? $request->input('admin_notes');

        Log::info('Admin rejecting enrollment', ['enrollment_request_id' => $enrollmentRequest->id, 'admin_id' => Auth::id(), 'note_present' => !empty($note)]);

        $enrollmentRequest->update([
            'status' => 'rejected',
            'admin_id' => Auth::id(),
            'admin_notes' => $note,
            'processed_at' => now(),
            'document_status' => 'rejected'
        ]);

        return back()->with('success', 'Enrollment request rejected.');
    }

    /**
     * Update overall document status based on individual document statuses
     */
    private function updateEnrollmentDocumentStatus(EnrollmentRequest $enrollmentRequest)
    {
        $documents = $enrollmentRequest->documents;
        
        if ($documents->isEmpty()) {
            $enrollmentRequest->update(['document_status' => 'not_submitted']);
            return;
        }

        $approvedCount = $documents->where('status', 'approved')->count();
        $rejectedCount = $documents->where('status', 'rejected')->count();
        $resubmissionCount = $documents->where('status', 'resubmission_required')->count();
        $pendingCount = $documents->where('status', 'pending')->count();

        if ($rejectedCount > 0 || $resubmissionCount > 0) {
            $status = 'resubmission_required';
        } elseif ($pendingCount > 0) {
            $status = 'under_review';
        } elseif ($approvedCount === $documents->count()) {
            $status = 'approved';
        } else {
            $status = 'under_review';
        }

        $enrollmentRequest->update([
            'document_status' => $status,
            'documents_reviewed_at' => now()
        ]);
    }
}
