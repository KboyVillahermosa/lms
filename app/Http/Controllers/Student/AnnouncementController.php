<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('author')
            ->published()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('students.announcements.index', compact('announcements'));
    }
}
