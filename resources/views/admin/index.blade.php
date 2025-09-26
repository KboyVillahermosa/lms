<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Users</h3>
                            <p class="text-sm text-gray-500">Manage users and roles</p>
                        </div>
                        <div class="text-3xl font-bold text-gray-700">{{ \App\Models\User::count() }}</div>
                    </div>

                    <div class="mt-4 flex items-center space-x-2">
                        <a href="{{ route('admin.users.index') }}" class="px-3 py-2 bg-indigo-600 text-white rounded-md text-sm">Manage</a>
                        <a href="{{ route('admin.users.create') }}" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-md text-sm">Create</a>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Courses</h3>
                            <p class="text-sm text-gray-500">Create and assign courses</p>
                        </div>
                        <div class="text-3xl font-bold text-gray-700">{{ \App\Models\Course::count() }}</div>
                    </div>

                    <div class="mt-4 flex items-center space-x-2">
                        <a href="{{ route('admin.courses.index') }}" class="px-3 py-2 bg-indigo-600 text-white rounded-md text-sm">Manage</a>
                        <a href="{{ route('admin.courses.create') }}" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-md text-sm">Create</a>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Assignments</h3>
                            <p class="text-sm text-gray-500">Create assignments and questions</p>
                        </div>
                        <div class="text-3xl font-bold text-gray-700">{{ \App\Models\Assignment::count() }}</div>
                    </div>

                    <div class="mt-4 flex items-center space-x-2">
                        <a href="{{ route('admin.assignments.index') }}" class="px-3 py-2 bg-indigo-600 text-white rounded-md text-sm">Manage</a>
                        <a href="{{ route('admin.assignments.create') }}" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-md text-sm">Create</a>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-700">{{ __("You're logged in!") }}</p>
                    <p class="mt-2 text-sm text-gray-500">Use the quick actions above to manage the system. More admin features will appear here as they are implemented.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
