<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssignmentSubmission;

class GradingController extends Controller
{
    public function index()
    {
        // show all ungraded submissions
        $submissions = AssignmentSubmission::with(['student', 'assignment'])->where('graded', false)->latest()->get();

        return view('admin.grading.index', compact('submissions'));
    }
}
