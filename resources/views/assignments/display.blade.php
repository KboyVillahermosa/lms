<x-app-layout>
    <x-slot name="header">Assignments</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($assignments as $assignment)
                    <a href="{{ route('assignments.display.show', $assignment) }}" class="block bg-white p-4 rounded-lg shadow hover:shadow-lg transition">
                        <h3 class="font-semibold text-lg">{{ $assignment->title }}</h3>
                        <p class="text-sm text-gray-500">{{ optional($assignment->course)->title }}</p>
                        <div class="mt-2 text-xs text-gray-600">Total Points: {{ $assignment->total_points }}</div>
                        <div class="mt-4 text-sm text-indigo-600">View details</div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">{{ $assignments->links() }}</div>
        </div>
    </div>
</x-app-layout>
