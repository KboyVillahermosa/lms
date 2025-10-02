<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\EnrollmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $enrollmentRequests = EnrollmentRequest::with(['course'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('students.enrollments.index', compact('enrollmentRequests'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Get active courses the user hasn't already requested enrollment for
        $requestedCourseIds = EnrollmentRequest::where('user_id', $user->id)->pluck('course_id');
        $courses = Course::active()
            ->whereNotIn('id', $requestedCourseIds)
            ->get();

        return view('students.enrollments.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'reason' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();

        // Check if user already has a request for this course
        $existingRequest = EnrollmentRequest::where('user_id', $user->id)
            ->where('course_id', $request->course_id)
            ->first();

        if ($existingRequest) {
            return redirect()->route('student.enrollments.index')
                ->with('error', 'You have already submitted an enrollment request for this course.');
        }

        EnrollmentRequest::create([
            'user_id' => $user->id,
            'course_id' => $request->course_id,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return redirect()->route('student.enrollments.index')
            ->with('success', 'Enrollment request submitted successfully!');
    }
}
