<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\User;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::withCount('questions')->paginate(15);
        return view('admin.quizes.index', compact('quizzes'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('admin.quizes.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:mcq,essay',
            'assigned_users' => 'nullable|array',
            'assigned_users.*' => 'exists:users,id',
            'questions' => 'nullable|array',
        ]);

        // per-question validation depending on quiz type
        if (!empty($data['questions']) && is_array($data['questions'])) {
            $questionErrors = [];
            foreach ($data['questions'] as $idx => $q) {
                $qIndex = intval($idx);
                // common: require question text
                if (empty($q['question'])) {
                    $questionErrors["questions.$idx.question"] = 'The question text is required.';
                    continue;
                }

                if ($data['type'] === 'mcq') {
                    // require options a-d and correct_option
                    foreach (['option_a','option_b','option_c','option_d'] as $opt) {
                        if (empty($q[$opt])) {
                            $questionErrors["questions.$idx.$opt"] = 'This option is required for MCQ questions.';
                        }
                    }
                    if (empty($q['correct_option']) || !in_array($q['correct_option'], ['a','b','c','d'])) {
                        $questionErrors["questions.$idx.correct_option"] = 'A valid correct option (a,b,c,d) is required for MCQ questions.';
                    }
                } else {
                    // essay: question text required; rubric optional
                }
            }

            if (!empty($questionErrors)) {
                return redirect()->back()->withInput()->withErrors($questionErrors);
            }
        }

        $quiz = Quiz::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
        ]);

        // attach assigned users
        if (!empty($data['assigned_users'])) {
            $quiz->users()->sync($data['assigned_users']);
        }

        // questions handling
        if (!empty($data['questions']) && is_array($data['questions'])) {
            foreach ($data['questions'] as $q) {
                // each question should be an array with keys depending on type
                $qData = array_merge(['quiz_id' => $quiz->id], $q);
                QuizQuestion::create($qData);
            }
        }

        return redirect()->route('admin.quizes.index')->with('success', 'Quiz created');
    }
}
