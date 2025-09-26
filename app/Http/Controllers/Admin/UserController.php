<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            'id_number' => 'nullable|string',
            'department' => 'nullable|string',
        ]);

        // Hash password before creating
        $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
        \App\Models\User::create($data);

        if (function_exists('toastr')) {
            toastr()->success('User created successfully!');
        }

        return redirect()->route('admin.users.index');
    }

    public function destroy(User $user)
    {
        $user->delete();
        if (function_exists('toastr')) {
            toastr()->success('User deleted successfully!');
        }

        return back();
    }
}
