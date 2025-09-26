<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignment;

class AssignmentDisplayController extends Controller
{
    public function index()
    {
        $query = Assignment::with('course')->latest();

        // if a student is logged in, show only assignments assigned to them or public ones
        if (auth()->check() && auth()->user()->role === 'student') {
            $userId = auth()->id();
            $query->where(function ($q) use ($userId) {
                $q->whereDoesntHave('students')
                  ->orWhereHas('students', function ($s) use ($userId) {
                      $s->where('users.id', $userId);
                  });
            });
        }

        $assignments = $query->paginate(12);
        return view('assignments.display', compact('assignments'));
    }

    public function show(Assignment $assignment)
    {
        $assignment->load('questions', 'course');
        return view('assignments.show', compact('assignment'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        // Ensure the assignment is visible to the current user (students only)
        if (! $assignment->isVisibleTo($request->user())) {
            abort(403);
        }

        $data = $request->validate([
            'answers' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('assignments/submissions', 'public');
        }

        $submission = \App\Models\AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'user_id' => $request->user()->id],
            [
                'answers' => $data['answers'] ?? null,
                'attachment' => $filePath,
                'submitted_at' => now(),
                'graded' => false,
            ]
        );

        if (app()->bound('flasher')) {
            toastr()->success('Assignment submitted.');
        }

        return redirect()->route('assignments.display.show', $assignment);
    }
}
