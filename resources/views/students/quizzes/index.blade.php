<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Quizzes</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold">Available Quizzes</h3>
                @if(session('success'))
                    <div class="mt-3 p-3 bg-green-50 border border-green-200 text-green-800 rounded">{{ session('success') }}</div>
                @endif
                <div class="mt-4">
                    @if(session('error'))
                        <div class="mt-3 p-3 bg-red-50 border border-red-200 text-red-800 rounded">{{ session('error') }}</div>
                    @endif
                    <ul class="space-y-3">
                        @foreach($quizzes as $quiz)
                            @php $sub = $submissions[$quiz->id] ?? null; @endphp
                            <li class="p-3 border rounded flex justify-between items-center">
                                <div>
                                    <div class="font-medium">{{ $quiz->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $quiz->description }}</div>
                                </div>
                                <div class="text-sm">
                                    @if($sub)
                                        @php $totalMcq = $quiz->mcq_count ?? $quiz->questions_count ?? 0; @endphp
                                        <div class="text-green-700">Submitted: {{ $sub->score ?? 0 }} / {{ $totalMcq }}</div>
                                        <a href="{{ route('student.quizzes.show', $quiz) }}" class="mt-1 inline-block px-3 py-1 bg-gray-100 text-gray-700 rounded">Review</a>
                                    @else
                                        <a href="{{ route('student.quizzes.show', $quiz) }}" class="px-3 py-1 bg-indigo-600 text-white rounded">Take Quiz</a>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-4">{{ $quizzes->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
