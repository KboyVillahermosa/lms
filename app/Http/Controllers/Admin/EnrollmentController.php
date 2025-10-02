<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnrollmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function index()
    {
        $enrollmentRequests = EnrollmentRequest::with(['user', 'course', 'admin'])
            ->orderBy('status', 'asc')  // pending first
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.enrollments.index', compact('enrollmentRequests'));
    }

    public function approve(Request $request, EnrollmentRequest $enrollmentRequest)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500'
        ]);

        $enrollmentRequest->update([
            'status' => 'approved',
            'admin_id' => Auth::id(),
            'admin_notes' => $request->admin_notes,
            'processed_at' => now()
        ]);

        // Add the student to the course
        $enrollmentRequest->course->students()->attach($enrollmentRequest->user_id);

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment request approved successfully!');
    }

    public function reject(Request $request, EnrollmentRequest $enrollmentRequest)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500'
        ]);

        $enrollmentRequest->update([
            'status' => 'rejected',
            'admin_id' => Auth::id(),
            'admin_notes' => $request->admin_notes,
            'processed_at' => now()
        ]);

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment request rejected.');
    }
}
