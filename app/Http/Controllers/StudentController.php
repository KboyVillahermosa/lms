<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
	public function index(Request $request)
	{
		$user = $request->user();
		return view('students.index', compact('user'));
	}
}
