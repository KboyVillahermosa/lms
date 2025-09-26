<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstructorController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        return view('instructor.index', compact('user'));
    }
}
