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
        // Redirect to new enrollment wizard
        return redirect()->route('student.enrollment.wizard')
            ->with('info', 'We\'ve upgraded our enrollment process! Please use the new application wizard.');
    }

    public function store(Request $request)
    {
        // Redirect to new enrollment wizard
        return redirect()->route('student.enrollment.wizard')
            ->with('info', 'Please use our new comprehensive enrollment application process.');
    }
}
