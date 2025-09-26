<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create User</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div><label>Name</label><input name="name" required></div>
                    <div><label>Email</label><input type="email" name="email" required></div>
                    <div><label>Password</label><input type="password" name="password" required></div>
                    <div><label>Role</label>
                        <select name="role">
                            <option value="student">Student</option>
                            <option value="instructor">Instructor</option>
                            <option value="admin">Admin</option>
                            <option value="registrar">Registrar</option>
                        </select>
                    </div>
                    <div><label>ID Number</label><input name="id_number"></div>
                    <div><label>Department</label><input name="department"></div>
                    <button type="submit">Create</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
