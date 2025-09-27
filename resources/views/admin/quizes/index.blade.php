<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Quizzes</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">All Quizzes</h3>
                    <a href="{{ route('admin.quizes.create') }}" class="px-3 py-2 bg-indigo-600 text-white rounded-md text-sm">Create Quiz</a>
                </div>

                <div class="mt-4">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="text-left">
                                <th class="p-2">Title</th>
                                <th class="p-2">Type</th>
                                <th class="p-2">Questions</th>
                                <th class="p-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quizzes as $quiz)
                                <tr class="border-t">
                                    <td class="p-2">{{ $quiz->title }}</td>
                                    <td class="p-2">{{ strtoupper($quiz->type) }}</td>
                                    <td class="p-2">{{ $quiz->questions_count }}</td>
                                    <td class="p-2">
                                        <!-- future actions -->
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $quizzes->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
