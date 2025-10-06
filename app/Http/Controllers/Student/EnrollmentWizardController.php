<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\DocumentType;
use App\Models\EnrollmentRequest;
use App\Models\EnrollmentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EnrollmentWizardController extends Controller
{
    /**
     * Start or continue enrollment process
     */
    public function index()
    {
        $user = auth()->user();
        
        // Check if user has a pending enrollment request
        $existingRequest = EnrollmentRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            // Continue existing enrollment
            return redirect()->route('student.enrollment.wizard.step', ['step' => $this->getNextStep($existingRequest)]);
        }

        // Start new enrollment
        return redirect()->route('student.enrollment.wizard.step', ['step' => 'personal-info']);
    }

    /**
     * Show specific step of enrollment wizard
     */
    public function showStep($step)
    {
        $user = auth()->user();
        $enrollmentRequest = $this->getOrCreateEnrollmentRequest($user);

        switch ($step) {
            case 'personal-info':
                return view('students.enrollment.wizard.personal-info', compact('enrollmentRequest'));
                
            case 'documents':
                $documentTypes = DocumentType::active()->orderBy('sort_order')->get();
                $uploadedDocuments = $enrollmentRequest->documents()->with('documentType')->get()->keyBy('document_type_id');
                return view('students.enrollment.wizard.documents', compact('enrollmentRequest', 'documentTypes', 'uploadedDocuments'));
                
            case 'course-selection':
                $courses = Course::active()->get();
                return view('students.enrollment.wizard.course-selection', compact('enrollmentRequest', 'courses'));
                
            case 'review':
                $enrollmentRequest->load(['course', 'documents.documentType']);
                return view('students.enrollment.wizard.review', compact('enrollmentRequest'));
                
            default:
                return redirect()->route('student.enrollment.wizard');
        }
    }

    /**
     * Save personal information step
     */
    public function savePersonalInfo(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other'
        ]);

        $user = auth()->user();
        $enrollmentRequest = $this->getOrCreateEnrollmentRequest($user);

        $enrollmentRequest->update([
            'phone' => $request->phone,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender
        ]);

        $enrollmentRequest->markStepCompleted('personal_info');

        return redirect()->route('student.enrollment.wizard.step', ['step' => 'documents'])
            ->with('success', 'Personal information saved successfully!');
    }

    /**
     * Upload document
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'document' => 'required|file|max:10240' // 10MB max
        ]);

        $user = auth()->user();
        $enrollmentRequest = $this->getOrCreateEnrollmentRequest($user);
        $documentType = DocumentType::findOrFail($request->document_type_id);

        // Validate file type
        $file = $request->file('document');
        $allowedFormats = $documentType->accepted_formats ?? [];
        
        if (!in_array(strtolower($file->getClientOriginalExtension()), $allowedFormats)) {
            return back()->withErrors(['document' => 'Invalid file format. Allowed formats: ' . implode(', ', $allowedFormats)]);
        }

        // Validate file size
        if ($file->getSize() > ($documentType->max_file_size * 1024)) {
            return back()->withErrors(['document' => 'File size too large. Maximum size: ' . $documentType->max_file_size_mb . 'MB']);
        }

        // Store file
        $originalName = $file->getClientOriginalName();
        $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('enrollment-documents', $storedName, 'private');

        // Delete existing document if any
        $existingDocument = $enrollmentRequest->documents()
            ->where('document_type_id', $request->document_type_id)
            ->first();

        if ($existingDocument) {
            Storage::disk('private')->delete($existingDocument->file_path);
            $existingDocument->delete();
        }

        // Create new document record
        EnrollmentDocument::create([
            'enrollment_request_id' => $enrollmentRequest->id,
            'document_type_id' => $request->document_type_id,
            'original_filename' => $originalName,
            'stored_filename' => $storedName,
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'status' => 'pending'
        ]);

        return back()->with('success', 'Document uploaded successfully!');
    }

    /**
     * Save course selection
     */
    public function saveCourseSelection(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'reason' => 'required|string|max:500'
        ]);

        $user = auth()->user();
        $enrollmentRequest = $this->getOrCreateEnrollmentRequest($user);

        $course = Course::findOrFail($request->course_id);
        
        if (!$course->is_active) {
            return back()->withErrors(['course_id' => 'Selected course is not available for enrollment.']);
        }

        $enrollmentRequest->update([
            'course_id' => $request->course_id,
            'reason' => $request->reason
        ]);

        $enrollmentRequest->markStepCompleted('course_selection');

        return redirect()->route('student.enrollment.wizard.step', ['step' => 'review'])
            ->with('success', 'Course selection saved successfully!');
    }

    /**
     * Submit enrollment application
     */
    public function submit(Request $request)
    {
        $user = auth()->user();
        $enrollmentRequest = $this->getOrCreateEnrollmentRequest($user);

        // Validate all required documents are uploaded
        $requiredDocuments = DocumentType::active()->required()->count();
        $uploadedDocuments = $enrollmentRequest->documents()->count();

        if ($uploadedDocuments < $requiredDocuments) {
            return back()->withErrors(['documents' => 'Please upload all required documents before submitting.']);
        }

        // Validate all required fields
        if (!$enrollmentRequest->course_id || !$enrollmentRequest->phone || !$enrollmentRequest->address) {
            return back()->withErrors(['submission' => 'Please complete all required steps before submitting.']);
        }

        // Mark as submitted
        $enrollmentRequest->update([
            'status' => 'pending',
            'document_status' => 'submitted',
            'documents_submitted_at' => now()
        ]);

        $enrollmentRequest->markStepCompleted('review');

        return redirect()->route('student.enrollment.status')
            ->with('success', 'Enrollment application submitted successfully! We will review your documents and notify you of the status.');
    }

    /**
     * Show enrollment status
     */
    public function status()
    {
        $user = auth()->user();
        $enrollmentRequests = EnrollmentRequest::where('user_id', $user->id)
            ->with(['course', 'documents.documentType'])
            ->latest()
            ->get();

        return view('students.enrollment.status', compact('enrollmentRequests'));
    }

    /**
     * Download uploaded document
     */
    public function downloadDocument(EnrollmentDocument $document)
    {
        $user = auth()->user();
        
        if ($document->enrollmentRequest->user_id !== $user->id) {
            abort(403);
        }

        return Storage::disk('private')->download($document->file_path, $document->original_filename);
    }

    /**
     * Get or create enrollment request for user
     */
    private function getOrCreateEnrollmentRequest($user)
    {
        return EnrollmentRequest::firstOrCreate(
            [
                'user_id' => $user->id,
                'status' => 'pending'
            ],
            [
                'document_status' => 'not_submitted'
            ]
        );
    }

    /**
     * Determine next step based on completion
     */
    private function getNextStep($enrollmentRequest)
    {
        if (!$enrollmentRequest->isStepCompleted('personal_info')) {
            return 'personal-info';
        }

        if (!$enrollmentRequest->hasAllRequiredDocuments()) {
            return 'documents';
        }

        if (!$enrollmentRequest->isStepCompleted('course_selection')) {
            return 'course-selection';
        }

        return 'review';
    }
}
