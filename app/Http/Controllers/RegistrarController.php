<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegistrarController extends Controller
{
	public function index(Request $request)
	{
		$user = $request->user();
		return view('registrar.index', compact('user'));
	}
}
