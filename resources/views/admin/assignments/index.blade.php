<x-app-layout>
    <x-slot name="header">Assignments</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium">All Assignments</h3>
                    <a href="{{ route('admin.assignments.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md">Create Assignment</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="text-left text-sm text-gray-600">
                                <th class="px-3 py-2">Title</th>
                                <th class="px-3 py-2">Course</th>
                                <th class="px-3 py-2">Total Points</th>
                                <th class="px-3 py-2">Due</th>
                                <th class="px-3 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignments as $assignment)
                                <tr class="border-t">
                                    <td class="px-3 py-2">{{ $assignment->title }}</td>
                                    <td class="px-3 py-2">{{ optional($assignment->course)->title }}</td>
                                    <td class="px-3 py-2">{{ $assignment->total_points }}</td>
                                    <td class="px-3 py-2">{{ $assignment->due_at ? $assignment->due_at->format('Y-m-d') : '-' }}</td>
                                    <td class="px-3 py-2">
                                        <a href="{{ route('admin.assignments.show', $assignment) }}" class="text-indigo-600">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $assignments->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
