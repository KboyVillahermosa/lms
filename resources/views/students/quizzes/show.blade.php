<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $quiz->title }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <p class="text-sm text-gray-600">{{ $quiz->description }}</p>

                @php
                    $user = Auth::user();
                    $latestSubmission = \App\Models\QuizSubmission::where('quiz_id', $quiz->id)->where('user_id', optional($user)->id)->orderByDesc('created_at')->first();
                    $answersMap = [];
                    if ($latestSubmission) {
                        $latestSubmission->load('answers');
                        foreach ($latestSubmission->answers as $a) {
                            $answersMap[$a->question_id] = $a;
                        }
                    }
                    $totalMcq = $quiz->questions->where('type','mcq')->count();
                @endphp

                @if($latestSubmission)
                    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 text-yellow-800">You already submitted this quiz. Your score: {{ $latestSubmission->score ?? 0 }} / {{ $totalMcq }} @if($latestSubmission->graded) (final) @else (prelim) @endif</div>
                @endif

                <form method="POST" action="{{ route('student.quizzes.submit', $quiz) }}" class="mt-4">
                    @csrf
                    @foreach($quiz->questions as $question)
                        <div class="p-3 border rounded mb-3">
                            <div class="font-medium">{{ $loop->iteration }}. {{ $question->question }}</div>
                            <div class="mt-2">
                                @if($question->type === 'mcq')
                                    <div class="space-y-2">
                                        @php $prev = $answersMap[$question->id] ?? null; @endphp
                                        <label class="block"><input type="radio" name="answers[{{ $question->id }}]" value="a" @if(optional($prev)->answer === 'a') checked @endif> A. {{ $question->option_a }} @if(optional($prev)->is_correct === true) <span class="text-green-600">(Correct)</span> @elseif(optional($prev)->is_correct === false) <span class="text-red-600">(Incorrect)</span> @endif</label>
                                        <label class="block"><input type="radio" name="answers[{{ $question->id }}]" value="b" @if(optional($prev)->answer === 'b') checked @endif> B. {{ $question->option_b }} @if(optional($prev)->is_correct === true) <span class="text-green-600">(Correct)</span> @elseif(optional($prev)->is_correct === false) <span class="text-red-600">(Incorrect)</span> @endif</label>
                                        <label class="block"><input type="radio" name="answers[{{ $question->id }}]" value="c" @if(optional($prev)->answer === 'c') checked @endif> C. {{ $question->option_c }} @if(optional($prev)->is_correct === true) <span class="text-green-600">(Correct)</span> @elseif(optional($prev)->is_correct === false) <span class="text-red-600">(Incorrect)</span> @endif</label>
                                        <label class="block"><input type="radio" name="answers[{{ $question->id }}]" value="d" @if(optional($prev)->answer === 'd') checked @endif> D. {{ $question->option_d }} @if(optional($prev)->is_correct === true) <span class="text-green-600">(Correct)</span> @elseif(optional($prev)->is_correct === false) <span class="text-red-600">(Incorrect)</span> @endif</label>
                                    </div>
                                @else
                                    <textarea name="answers[{{ $question->id }}]" class="mt-2 block w-full border-gray-300 rounded-md" rows="5">{{ optional($answersMap[$question->id])->answer ?? '' }}</textarea>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded" @if($latestSubmission) disabled @endif>{{ $latestSubmission ? 'Already submitted' : 'Submit Quiz' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Results Modal -->
    @if(session('show_results'))
        @php
            $resScore = session('score');
            $resMax = session('max_score');
            $resSubmissionId = session('submission_id');
            // load submitted answers for this id
            $resSubmission = \App\Models\QuizSubmission::with('answers')->find($resSubmissionId);
            $resAnswers = [];
            if ($resSubmission) {
                foreach ($resSubmission->answers as $a) {
                    $resAnswers[$a->question_id] = $a;
                }
            }
        @endphp

        <div id="results-modal" class="fixed inset-0 flex items-center justify-center z-50 px-4">
            <!-- softer, lighter backdrop instead of heavy black -->
            <div id="results-backdrop" class="absolute inset-0 bg-black opacity-0 transition-opacity duration-300 z-40"></div>
            <div id="results-content" class="relative bg-white rounded-lg p-8 z-50 max-w-xl w-full mx-auto shadow-lg transform opacity-0 translate-y-6 scale-95 transition-all duration-300 ease-out">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Quiz Results</h3>
                    <button id="close-results" class="text-gray-500">Close</button>
                </div>

                <div class="mt-4">
                    <div class="text-2xl font-bold">Your score: {{ $resScore }} / {{ $resMax }}</div>
                    <p class="text-sm text-gray-600 mt-2">Below shows which MCQs you got right (green) or wrong (red). Essay answers are saved for manual grading.</p>

                    <div class="mt-4 space-y-3">
                        @foreach($quiz->questions as $question)
                            @php $ra = $resAnswers[$question->id] ?? null; @endphp
                            <div class="p-3 border rounded">
                                <div class="font-medium">{{ $loop->iteration }}. {{ $question->question }}</div>
                                @if($question->type === 'mcq')
                                    @php
                                        $given = optional($ra)->answer;
                                        $isCorrect = optional($ra)->is_correct;
                                        $correct = $question->correct_option;
                                        $optionText = function($letter) use ($question) {
                                            if (!$letter) return null;
                                            $letter = strtolower($letter);
                                            return match($letter) {
                                                'a' => $question->option_a,
                                                'b' => $question->option_b,
                                                'c' => $question->option_c,
                                                'd' => $question->option_d,
                                                default => null,
                                            };
                                        };
                                        $givenText = $optionText($given);
                                        $correctText = $optionText($correct);
                                        $answerClass = $isCorrect === true ? 'text-green-700' : ($isCorrect === false ? 'text-red-600' : 'text-gray-600');
                                    @endphp
                                    <div class="mt-2">
                                        <div class="text-sm">Your answer: <span class="font-medium {{ $answerClass }}">{{ strtoupper($given ?? '-') }}</span>@if($givenText) — <span class="{{ $answerClass }}">{{ $givenText }}</span>@endif</div>
                                        <div class="mt-1 text-sm text-gray-600">Correct answer: <span class="font-medium text-green-700">{{ strtoupper($correct ?? '-') }}</span>@if($correctText) — <span class="text-green-700">{{ $correctText }}</span>@endif</div>
                                    </div>
                                @else
                                    <div class="mt-2 text-sm text-gray-700">Essay answer saved for manual grading.</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function(){
                var modal = document.getElementById('results-modal');
                var content = document.getElementById('results-content');
                var close = document.getElementById('close-results');
                var backdrop = document.getElementById('results-backdrop');

                function show(){
                    // run on next frame so transitions apply
                    requestAnimationFrame(function(){
                        content.classList.remove('opacity-0','translate-y-6','scale-95');
                        content.classList.add('opacity-100','translate-y-0','scale-100');
                        backdrop.classList.add('opacity-40');
                    });
                }

                function hide(){
                    // play exit animation then remove modal
                    content.classList.remove('opacity-100','translate-y-0','scale-100');
                    content.classList.add('opacity-0','translate-y-6','scale-95');
                    backdrop.classList.remove('opacity-40');
                    setTimeout(function(){ if(modal) modal.remove(); }, 300);
                }

                if(close) close.addEventListener('click', hide);
                if(backdrop) backdrop.addEventListener('click', hide);
                document.addEventListener('keydown', function(e){ if(e.key === 'Escape') hide(); });

                // start enter animation
                show();
            })();
        </script>
    @endif
</x-app-layout>
