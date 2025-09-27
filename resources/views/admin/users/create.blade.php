<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create User</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input name="name" required class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary/50" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary/50" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary/50" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary/50">
                            <option value="student">Student</option>
                            <option value="instructor">Instructor</option>
                            <option value="admin">Admin</option>
                            <option value="registrar">Registrar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">ID Number</label>
                        <input name="id_number" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary/50" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Department</label>
                        <input name="department" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary/50" />
                    </div>
                    <div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-700 text-white rounded-md text-sm font-medium">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
