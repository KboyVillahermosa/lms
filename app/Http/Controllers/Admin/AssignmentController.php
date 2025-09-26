<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\Course;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::with('course')->latest()->paginate(15);

        return view('admin.assignments.index', compact('assignments'));
    }

    public function create()
    {
        return view('admin.assignments.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'nullable|exists:courses,id',
            'instructions' => 'nullable|string',
            'total_points' => 'required|integer|min:1',
            'due_at' => 'nullable|date',
        ]);

        $assignment = Assignment::create($data);

        // sync students if provided
        if ($request->filled('students')) {
            $assignment->students()->sync($request->input('students'));
        }

        if (app()->bound('flasher')) {
            toastr()->success('Assignment created successfully. You can now add questions.');
        }

        // Redirect admin directly to the focused "add question" flow to start Q1
        return redirect()->route('admin.assignments.questions.create', $assignment);
    }

    public function show(Assignment $assignment)
    {
        $assignment->load('questions', 'course');
        return view('admin.assignments.show', compact('assignment'));
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        if (app()->bound('flasher')) {
            toastr()->success('Assignment deleted.');
        }

        return redirect()->route('admin.assignments.index');
    }

    /**
     * Attach additional students to an assignment (from the admin display page).
     */
    public function addStudents(Request $request, Assignment $assignment)
    {
        $data = $request->validate([
            'students' => 'required|array',
            'students.*' => 'exists:users,id'
        ]);

        // Attach without detaching existing students
        $assignment->students()->syncWithoutDetaching($data['students']);

        if (app()->bound('flasher')) {
            toastr()->success('Students added to assignment.');
        }

        return redirect()->route('admin.assignments.show', $assignment);
    }
}
