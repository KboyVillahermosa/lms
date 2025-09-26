<x-app-layout>
    <x-slot name="header">Assignment: {{ $assignment->title }}</x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6 space-y-6">
                <div>
                    <h3 class="text-lg font-medium">{{ $assignment->title }}</h3>
                    <p class="text-sm text-gray-600">Course: {{ optional($assignment->course)->title }}</p>
                    <p class="text-sm text-gray-600">Total Points: {{ $assignment->total_points }}</p>
                </div>

                <div>
                    <h4 class="font-semibold">Instructions</h4>
                    <div class="mt-2 text-sm text-gray-700">{!! nl2br(e($assignment->instructions)) !!}</div>
                </div>

                <div>
                    <h4 class="font-semibold">Questions</h4>
                    <div class="mt-2">
                        @if($assignment->questions->isEmpty())
                            <p class="text-sm text-gray-600">No questions yet.</p>
                        @else
                            <ol class="list-decimal ml-5 space-y-2">
                                @foreach($assignment->questions as $q)
                                    <li>
                                        <div>
                                            <div class="text-sm">{!! nl2br(e($q->question)) !!}</div>
                                            <div class="text-xs text-gray-500">Points: {{ $q->points }}</div>
                                            @if($q->attachment)
                                                <div class="mt-1 text-sm">
                                                    <a href="{{ asset('storage/' . $q->attachment) }}" target="_blank" class="text-indigo-600">View attachment</a>
                                                </div>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                </div>

                @auth
                    @if(auth()->user()->role === 'student')
                        <div class="mt-4 border-t pt-4">
                            <h4 class="font-semibold">Your Submission</h4>
                            @php $submission = \App\Models\AssignmentSubmission::where('assignment_id', $assignment->id)->where('user_id', auth()->id())->first(); @endphp

                            @if($submission)
                                <div class="text-sm">Submitted: {{ optional($submission->submitted_at)->toDayDateTimeString() }}</div>
                                @if($submission->attachment)
                                    <div><a href="{{ asset('storage/' . $submission->attachment) }}" target="_blank" class="text-indigo-600">Download your attachment</a></div>
                                @endif
                                <div class="mt-2">Answers: <div class="text-sm text-gray-700">{!! nl2br(e($submission->answers)) !!}</div></div>
                                <div class="mt-2">Status: @if($submission->graded) <span class="text-green-600">Graded ({{ $submission->grade }})</span> @else <span class="text-yellow-600">Pending grading</span> @endif</div>
                                @if($submission->graded)
                                    <div class="mt-2">Feedback: <div class="text-sm text-gray-700">{!! nl2br(e($submission->feedback)) !!}</div></div>
                                @endif
                            @else
                                <p class="text-sm text-gray-600">You have not submitted this assignment yet. Use the form below to submit your answers and optional attachment.</p>
                            @endif

                            <div class="mt-3">
                                <form action="{{ route('assignments.submit', $assignment) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div>
                                        <label class="block text-sm">Answers</label>
                                        <textarea name="answers" class="w-full border rounded p-2" rows="6">{{ old('answers', optional($submission)->answers) }}</textarea>
                                    </div>
                                    <div class="mt-2">
                                        <label class="block text-sm">Attachment (optional)</label>
                                        <input type="file" name="attachment" accept="application/pdf,image/*" />
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Submit Assignment</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</x-app-layout>
