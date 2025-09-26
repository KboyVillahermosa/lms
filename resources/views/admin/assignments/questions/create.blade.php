<x-app-layout>
    <x-slot name="header">Add Question — {{ $assignment->title }}</x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                @if(session('success'))
                    <div class="mb-4 text-sm text-green-600">{{ session('success') }}</div>
                @endif

                <form action="{{ route('admin.assignments.questions.store', $assignment) }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Question</label>
                            <textarea name="question" class="mt-1 block w-full border-gray-300 rounded-md" rows="6">{{ old('question') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Points</label>
                            <input type="number" name="points" value="{{ old('points', 1) }}" class="mt-1 block w-32 border-gray-300 rounded-md" />
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600">Attachment (optional)</label>
                            <input type="file" name="attachment" accept="application/pdf,image/*" />
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('admin.assignments.index') }}" class="text-sm text-gray-600">Back to assignments</a>
                            <div class="space-x-2">
                                <button type="submit" name="action" value="done" class="px-4 py-2 bg-gray-100 text-gray-700 rounded">Create</button>
                                <button type="submit" name="action" value="next" class="px-4 py-2 bg-indigo-600 text-white rounded">Next</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
