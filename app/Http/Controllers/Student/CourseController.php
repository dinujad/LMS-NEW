<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Tute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        // Show all published courses
        $courses = Course::where('is_published', true)->withCount('tutes')->latest()->paginate(9);
        return view('student.courses.index', compact('courses'));
    }

    public function myCourses()
    {
        $user = Auth::user();
        $enrolledCourses = $user->courses; // Via many-to-many relationship helper in User model
        return view('student.courses.my', compact('enrolledCourses'));
    }

    public function show(Course $course)
    {
        if (!$course->is_published) {
            return redirect()
                ->route('student.courses.index')
                ->with('error', 'This course is not published yet. Ask the admin to enable “Publish Course” for it.');
        }

        $user = Auth::user();
        
        // Check Enrollment
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        $isEnrolled = $enrollment && $enrollment->status === 'active';
        $isPending = $enrollment && $enrollment->status === 'pending'; // Or check pending payment
        
        // Check if there is a pending payment even if enrollment isn't active
        $pendingPayment = Payment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'pending')
            ->exists();

        if ($isEnrolled) {
            $course->load(['tutes' => function($q) {
                $q->where('is_published', true)->orderBy('order');
            }, 'notices' => function($q) {
                $q->where('is_published', true);
            }, 'exams' => function($q) {
                $q->where('is_published', true);
            }]);
        }

        return view('student.courses.show', compact('course', 'isEnrolled', 'isPending', 'pendingPayment'));
    }

    /**
     * Lesson media URLs for enrolled students only (keeps YouTube/Vimeo links out of page HTML).
     */
    public function tutePayload(Course $course, Tute $tute)
    {
        $user = Auth::user();

        $enrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        if (! $enrolled) {
            return response()->json(['message' => 'You are not enrolled in this course.'], 403);
        }

        // Compare as int: MySQL may return course_id as string; strict !== fails only on production.
        if ((int) $tute->course_id !== (int) $course->id || ! $tute->is_published) {
            return response()->json(['message' => 'Lesson not found.'], 404);
        }

        return response()->json([
            'type' => $tute->type,
            'title' => $tute->title,
            'video_url' => $tute->video_url,
            'file_url' => $tute->file_path ? asset('storage/'.$tute->file_path) : null,
        ]);
    }

    public function enroll(Request $request, Course $course)
    {
        $user = Auth::user();

        // Check if already enrolled
        if (Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->where('status', 'active')->exists()) {
            return back()->with('success', 'You are already enrolled.');
        }

        // Free Course Logic
        if ($course->price <= 0) {
            Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'status' => 'active',
                'enrolled_at' => now(),
            ]);
            return redirect()->route('student.courses.show', $course)->with('success', 'Enrolled successfully in free course.');
        }

        // Paid Course Logic
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'proof_image' => 'required|image|max:2048', // Bank Slip
            'transaction_ref' => 'nullable|string'
        ]);

        if ($validated['amount'] < $course->price) {
            return back()->with('error', 'Payment amount is less than course price.');
        }

        // Store Payment
        $path = $request->file('proof_image')->store('payment_slips', 'public');

        Payment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount' => $validated['amount'],
            'method' => 'bank_transfer',
            'proof_image' => $path,
            'transaction_ref' => $request->transaction_ref,
            'status' => 'pending'
        ]);

        // Create Pending Enrollment (Optional, can wait for approval loop)
        // For logic simplicity, we rely on Payment Approval to create the enrollment in Admin/PaymentController.
        
        return back()->with('success', 'Payment slip uploaded. Waiting for admin approval.');
    }
}
