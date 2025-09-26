<x-app-layout>
    <x-slot name="header">Create Assignment</x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form action="{{ route('admin.assignments.store') }}" method="post">
                    @csrf

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Titles</label>
                            <input name="title" value="{{ old('title') }}" class="mt-1 block w-full border-gray-300 rounded-md" />
                        </div>

                        <!-- course is optional; assignments can be created without selecting a course -->

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Points</label>
                            <input name="total_points" value="{{ old('total_points', 100) }}" type="number" class="mt-1 block w-full border-gray-300 rounded-md" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Due at (optional)</label>
                            <input name="due_at" value="{{ old('due_at') }}" type="date" class="mt-1 block w-full border-gray-300 rounded-md" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Instructions</label>
                            <textarea name="instructions" class="mt-1 block w-full border-gray-300 rounded-md">{{ old('instructions') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Assign to students (optional)</label>
                            <select name="students[]" class="mt-1 block w-full border-gray-300 rounded-md" multiple>
                                @foreach(\App\Models\User::where('role','student')->get() as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Leave empty to make assignment visible to everyone.</p>
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('admin.assignments.index') }}" class="mr-2 text-sm text-gray-600">Cancel</a>
                            <button class="px-4 py-2 bg-indigo-600 text-white rounded-md">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
