<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                    @php
                        $user = Auth::user();
                        $visibleAssignments = [];
                        $submissions = collect();
                        if ($user) {
                            $visibleAssignments = \App\Models\Assignment::where(function($q) use ($user) {
                                $q->whereDoesntHave('students')
                                  ->orWhereHas('students', function($s) use ($user) { $s->where('users.id', $user->id); });
                            })->latest()->get();

                            $submissions = \App\Models\AssignmentSubmission::with('assignment')->where('user_id', $user->id)->latest()->get();
                        }
                    @endphp

                    <div class="mt-6">
                        <h3 class="font-semibold">Pending Assignments</h3>
                        @if($visibleAssignments->isEmpty())
                            <p class="text-sm text-gray-600">No assignments available.</p>
                        @else
                            <ul class="mt-3 space-y-2">
                                @foreach($visibleAssignments as $a)
                                    @php $sub = $submissions->firstWhere('assignment_id', $a->id); @endphp
                                    <li class="p-3 border rounded flex justify-between items-center">
                                        <div>
                                            <div class="font-medium">{{ $a->title }}</div>
                                            <div class="text-xs text-gray-500">Due: {{ optional($a->due_at)->toFormattedDateString() ?? 'No due date' }}</div>
                                        </div>
                                        <div class="text-sm">
                                            @if($sub)
                                                @if($sub->graded)
                                                    <span class="text-green-600">Graded: {{ $sub->grade }} / {{ $a->total_points }}</span>
                                                @else
                                                    <span class="text-yellow-600">Submitted (pending grading)</span>
                                                @endif
                                            @else
                                                <a href="{{ route('assignments.display.show', $a) }}" class="px-3 py-1 bg-indigo-600 text-white rounded">Open & Submit</a>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div class="mt-6">
                        <h3 class="font-semibold">Submission History</h3>
                        @if($submissions->isEmpty())
                            <p class="text-sm text-gray-600">No submissions yet.</p>
                        @else
                            <ul class="mt-3 space-y-2">
                                @foreach($submissions as $s)
                                    <li class="p-3 border rounded">
                                        <div class="flex justify-between">
                                            <div>
                                                <div class="font-medium">{{ optional($s->assignment)->title }}</div>
                                                <div class="text-xs text-gray-500">Submitted: {{ optional($s->submitted_at)->toDayDateTimeString() }}</div>
                                                @if($s->graded)
                                                    <div class="text-sm text-green-600">Grade: {{ $s->grade }} / {{ optional($s->assignment)->total_points }}</div>
                                                    <div class="text-sm text-gray-700">Feedback: {!! nl2br(e($s->feedback)) !!}</div>
                                                @else
                                                    <div class="text-sm text-yellow-600">Pending grading</div>
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
        </div>
    </div>
</x-app-layout>
