<x-app-layout>
    <x-slot name="header">Assignment: {{ $assignment->title }}</x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium">{{ $assignment->title }}</h3>
                        <p class="text-sm text-gray-600">Course: {{ optional($assignment->course)->title }}</p>
                        <p class="text-sm text-gray-600">Total Points: {{ $assignment->total_points }}</p>
                    </div>
                    <div>
                        <form action="{{ route('admin.assignments.destroy', $assignment) }}" method="post" onsubmit="return confirm('Delete this assignment?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1 bg-red-600 text-white rounded">Delete</button>
                        </form>
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold">Instructions</h4>
                    <div class="mt-2 text-sm text-gray-700">{!! nl2br(e($assignment->instructions)) !!}</div>
                </div>

                <div>
                    <h4 class="font-semibold">Questions</h4>
                    <div class="mt-2">
                        @if($assignment->questions->isEmpty())
                            <p class="text-sm text-gray-600">No questions yet. Add one below.</p>
                        @else
                            <ol class="list-decimal ml-5 space-y-2">
                                @foreach($assignment->questions as $q)
                                    <li>
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="text-sm">{!! nl2br(e($q->question)) !!}</div>
                                                <div class="text-xs text-gray-500">Points: {{ $q->points }}</div>
                                                @if($q->attachment)
                                                    <div class="mt-1 text-sm">
                                                        <a href="{{ asset('storage/' . $q->attachment) }}" target="_blank" class="text-indigo-600">View attachment</a>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <form action="{{ route('admin.assignments.questions.destroy', [$assignment, $q]) }}" method="post" onsubmit="return confirm('Remove question?')">
                                                    @csrf @method('DELETE')
                                                    <button class="text-sm text-red-600">Remove</button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold">Assigned Students</h4>
                    @if($assignment->students()->count() > 0)
                        <ul class="mt-2">
                            @foreach($assignment->students as $student)
                                <li class="text-sm text-gray-700">{{ $student->name }} &lt;{{ $student->email }}&gt;</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-sm text-gray-500">This assignment is visible to everyone (no specific students assigned).</div>
                    @endif
                </div>

                <div>
                    <h4 class="font-semibold">Add Students</h4>
                    @php $students = \App\Models\User::where('role', 'student')->get(); @endphp
                    <form action="{{ route('admin.assignments.students', $assignment) }}" method="POST" class="mt-2">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Select students to add</label>
                            <select name="students[]" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @foreach($students as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Add Students</button>
                        </div>
                    </form>
                </div>

                <div>
                    <h4 class="font-semibold">Add Question</h4>
                    <form action="{{ route('admin.assignments.questions.store', $assignment) }}" method="post" class="mt-2 space-y-3" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <textarea name="question" class="w-full border-gray-300 rounded-md" placeholder="Question text">{{ old('question') }}</textarea>
                        </div>
                        <div>
                            <input name="points" type="number" value="{{ old('points', 1) }}" class="border-gray-300 rounded-md" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600">Attachment (optional) — PDF or images, max 5MB</label>
                            <input type="file" name="attachment" accept="application/pdf,image/*" />
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="submit" name="action" value="done" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md">Create</button>
                            <button type="submit" name="action" value="next" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Next</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
