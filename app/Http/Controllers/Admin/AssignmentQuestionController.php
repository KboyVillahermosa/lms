<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\AssignmentQuestion;

class AssignmentQuestionController extends Controller
{
    public function store(Request $request, Assignment $assignment)
    {
        $data = $request->validate([
            'question' => 'required|string',
            'points' => 'required|integer|min:1',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif,svg|max:5120',
        ]);

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('assignment_questions', 'public');
            $data['attachment'] = $path;
        }

        $data['assignment_id'] = $assignment->id;

        AssignmentQuestion::create($data);

        if (app()->bound('flasher')) {
            toastr()->success('Question added.');
        }

        // if the user clicked "Next" (previously named add_next) send them back to the question create form
        if ($request->input('action') === 'next' || $request->input('action') === 'add_next') {
            return redirect()->route('admin.assignments.questions.create', $assignment)->with('success', 'Question added — add another.');
        }

        // done/create -> go to public assignment display (card grid)
        return redirect()->route('assignments.display');
    }

    public function create(Assignment $assignment)
    {
        // show a focused form for adding a single question; after saving the controller
        // can redirect back here so admin can add Q2, Q3 quickly.
        return view('admin.assignments.questions.create', compact('assignment'));
    }

    public function destroy(Assignment $assignment, AssignmentQuestion $question)
    {
        // ensure relationship
        if ($question->assignment_id !== $assignment->id) {
            abort(404);
        }

        // remove attachment from storage if exists
        if ($question->attachment) {
            \Storage::disk('public')->delete($question->attachment);
        }

        $question->delete();

        if (app()->bound('flasher')) {
            toastr()->success('Question removed.');
        }

        return redirect()->route('admin.assignments.show', $assignment);
    }
}
