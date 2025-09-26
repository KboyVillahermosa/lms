<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('instructor')->orderBy('id', 'desc')->paginate(20);
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $instructors = User::where('role', 'instructor')->get();
        return view('admin.courses.create', compact('instructors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|unique:courses,code',
            'department' => 'nullable|string',
            'description' => 'nullable|string',
            'instructor_id' => 'nullable|exists:users,id',
            'capacity' => 'nullable|integer',
        ]);

        Course::create($data);

        // Show a toast notification (requires yoeunes/toastr installed and flasher:install run)
        if (function_exists('toastr')) {
            toastr()->success('Course created successfully!');
        }

        return redirect()->route('admin.courses.index');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        if (function_exists('toastr')) {
            toastr()->success('Course deleted successfully!');
        }

        return back();
    }
}
