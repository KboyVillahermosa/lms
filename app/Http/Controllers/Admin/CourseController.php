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
        $courses = Course::with('instructor')->orderBy('id', 'desc')->get();
        $instructors = User::where('role', 'instructor')->get();
        return view('admin.courses.index', compact('courses', 'instructors'));
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

        // Handle checkbox properly - if not present, it's false
        $data['is_active'] = $request->has('is_active') ? true : false;

        Course::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Course created successfully!']);
        }

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully!');
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|unique:courses,code,' . $course->id,
            'department' => 'nullable|string',
            'description' => 'nullable|string',
            'instructor_id' => 'nullable|exists:users,id',
            'capacity' => 'nullable|integer',
        ]);

        // Handle checkbox properly - if not present, it's false
        $data['is_active'] = $request->has('is_active') ? true : false;

        $course->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Course updated successfully!']);
        }

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully!');
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
