<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'total_students' => User::where('role', 'student')->count(),
            'total_courses' => Course::count(),
            'total_enrollments' => Enrollment::where('status', 'active')->count(),
            'total_revenue' => Payment::where('status', 'approved')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'upcoming_exams' => Exam::where('start_time', '>', now())->count(),
            'recent_payments' => Payment::with('user', 'course')->latest()->take(5)->get(),
            'recent_users' => User::where('role', 'student')->latest()->take(5)->get()
        ];

        return view('admin.dashboard', compact('data'));
    }
}
