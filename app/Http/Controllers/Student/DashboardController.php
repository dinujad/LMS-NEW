<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Active Enrollments
        $enrolledCourseIds = Enrollment::where('user_id', $user->id)
                                      ->where('status', 'active')
                                      ->pluck('course_id');
        
        $my_courses_count = $enrolledCourseIds->count();
        
        // Fetch Notices (Enrolled Courses + Global)
        $notices = \App\Models\Notice::where(function($query) use ($enrolledCourseIds) {
                $query->whereIn('course_id', $enrolledCourseIds)
                      ->orWhereNull('course_id');
            })
            ->where('is_published', true)
            ->latest()
            ->take(5)
            ->get();

        // Fetch Active Exams (Enrolled Courses + Not Expired)
        $exams = \App\Models\Exam::with('course')
            ->whereIn('course_id', $enrolledCourseIds)
            ->where('is_published', true)
            ->where('end_time', '>', now())
            ->orderBy('start_time', 'asc')
            ->take(5)
            ->get();

        return view('student.dashboard', compact('my_courses_count', 'notices', 'exams'));
    }
}
