<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizSubmission;
use App\Models\QuizAnswer;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index()
    {
        $user = Auth::user();
                // quizzes assigned to user or public (no assigned users)
                $quizzes = Quiz::where(function($q) use ($user) {
                        $q->whereDoesntHave('users')
                            ->orWhereHas('users', function($s) use ($user) { $s->where('users.id', $user->id); });
                })
                ->withCount('questions')
                ->withCount(['questions as mcq_count' => function($q){ $q->where('type','mcq'); }])
                ->paginate(12);

        // load student's latest submission per quiz
        $quizIds = $quizzes->pluck('id')->all();
        $submissions = \App\Models\QuizSubmission::whereIn('quiz_id', $quizIds)
            ->where('user_id', $user->id)
            ->get()
            ->groupBy('quiz_id')
            ->map(function($group){
                return $group->sortByDesc('created_at')->first();
            });

        return view('students.quizzes.index', compact('quizzes','submissions'));
    }

    public function show(Quiz $quiz)
    {
        $quiz->load('questions');
        return view('students.quizzes.show', compact('quiz'));
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $user = Auth::user();
        $payload = $request->validate([
            'answers' => 'required|array'
        ]);

        try {
            // prevent duplicate submissions
            $existing = QuizSubmission::where('quiz_id', $quiz->id)->where('user_id', $user->id)->first();
            if ($existing) {
                return redirect()->route('student.quizzes.index')->with('error', 'You have already submitted this quiz.');
            }

            // create submission
            $submission = QuizSubmission::create([
                'quiz_id' => $quiz->id,
                'user_id' => $user->id,
                'score' => 0,
                'graded' => false,
            ]);
        } catch (\Exception $e) {
            \Log::error('Quiz submission failed to create', ['error' => $e->getMessage(), 'quiz_id' => $quiz->id, 'user_id' => $user->id]);
            return redirect()->back()->withInput()->with('error', 'Failed to record submission. Please try again.');
        }

        $totalScore = 0;
        $maxScore = 0;

        foreach ($quiz->questions as $question) {
            $qId = $question->id;
            $answerVal = $payload['answers'][$qId] ?? null;

            $isCorrect = null;
            $points = 0;

            if ($question->type === 'mcq') {
                // auto-grade
                $isCorrect = (!empty($answerVal) && $answerVal === $question->correct_option);
                $points = $isCorrect ? 1 : 0; // 1 point per MCQ for now
                $totalScore += $points;
                $maxScore += 1;
            } else {
                // essay: store answer for manual grading later
                $isCorrect = null;
                $points = null;
            }

            QuizAnswer::create([
                'submission_id' => $submission->id,
                'question_id' => $qId,
                'answer' => is_array($answerVal) ? json_encode($answerVal) : $answerVal,
                'is_correct' => $isCorrect,
                'points_awarded' => $points,
            ]);
        }

        // set score and graded flag depending on presence of essay questions
        $hasEssay = $quiz->questions->contains(function($q){ return $q->type === 'essay'; });
        $submission->score = $totalScore;
        $submission->graded = !$hasEssay; // if no essay, fully graded
        $submission->save();

        // redirect back to show page and trigger results modal
        return redirect()->route('student.quizzes.show', $quiz)->with([
            'show_results' => true,
            'submission_id' => $submission->id,
            'score' => $totalScore,
            'max_score' => $maxScore,
        ]);
    }
}
