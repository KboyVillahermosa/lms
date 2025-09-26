<x-app-layout>
    <x-slot name="header">Submissions for: {{ $assignment->title }}</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                @if($submissions->isEmpty())
                    <p>No submissions yet.</p>
                @else
                    <ul>
                        @foreach($submissions as $s)
                            <li class="mb-4 border-b pb-2">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-medium">{{ $s->student->name }} ({{ $s->student->email }})</div>
                                        <div class="text-sm text-gray-600">Submitted: {{ optional($s->submitted_at)->toDayDateTimeString() }}</div>
                                        @if($s->attachment)
                                            <div><a href="{{ asset('storage/' . $s->attachment) }}" target="_blank" class="text-indigo-600">Download attachment</a></div>
                                        @endif
                                        <div class="mt-2">Answers: <div class="text-sm text-gray-700">{!! nl2br(e($s->answers)) !!}</div></div>
                                    </div>
                                    <div>
                                        @if($s->graded)
                                            <div class="text-sm text-green-600">Graded: {{ $s->grade }} / {{ $assignment->total_points }}</div>
                                            <div class="text-xs text-gray-600">By: {{ optional($s->grader)->name }} on {{ optional($s->graded_at)->toDayDateTimeString() }}</div>
                                            <div class="mt-2 text-sm">Feedback: {!! nl2br(e($s->feedback)) !!}</div>
                                        @else
                                            <form action="{{ route('admin.assignments.submissions.grade', [$assignment, $s]) }}" method="POST" class="space-y-2">
                                                @csrf
                                                <div>
                                                    <label class="block text-sm">Grade (max {{ $assignment->total_points }})</label>
                                                    <input type="number" name="grade" max="{{ $assignment->total_points }}" class="border rounded px-2 py-1" />
                                                </div>
                                                <div>
                                                    <label class="block text-sm">Feedback</label>
                                                    <textarea name="feedback" class="border rounded w-56"></textarea>
                                                </div>
                                                <div>
                                                    <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">Save Grade</button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
