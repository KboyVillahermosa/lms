<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;

class AssignmentSubmissionController extends Controller
{
    public function index(Assignment $assignment)
    {
        $submissions = AssignmentSubmission::with('student')->where('assignment_id', $assignment->id)->latest()->get();
        return view('admin.assignments.submissions.index', compact('assignment', 'submissions'));
    }

    public function grade(Request $request, Assignment $assignment, AssignmentSubmission $submission)
    {
        $data = $request->validate([
            'grade' => 'required|integer|min:0|max:' . $assignment->total_points,
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'graded' => true,
            'grade' => $data['grade'],
            'feedback' => $data['feedback'] ?? null,
            'graded_by' => $request->user()->id,
            'graded_at' => now(),
        ]);

        if (app()->bound('flasher')) {
            toastr()->success('Submission graded.');
        }

        return redirect()->route('admin.assignments.submissions.index', $assignment);
    }
}
