<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Admin\NoticeController as AdminNoticeController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\ExamController as StudentExamController;

// Auth::routes(['verify' => true]); // Disabling default routes to use custom ones

// මේ Route එක තාවකාලිකව දාන්න
Route::get('/check-models', function () {
    $apiKey = env('GEMINI_API_KEY'); // .env එකේ Key එක ගන්නවා

    // Google API එකෙන් Models ලිස්ට් එක ඉල්ලමු
    $response = Http::withoutVerifying()
        ->get("https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}");

    return $response->json();
});

// --- ADMIN AUTH ---
Route::get('admin/login', [App\Http\Controllers\Auth\AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [App\Http\Controllers\Auth\AdminLoginController::class, 'login']);
Route::post('admin/logout', [App\Http\Controllers\Auth\AdminLoginController::class, 'logout'])->name('admin.logout');

// --- STUDENT AUTH ---
// Password Reset
Route::get('password/reset', [App\Http\Controllers\Auth\StudentAuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('password/email', [App\Http\Controllers\Auth\StudentAuthController::class, 'sendResetOtp'])->name('password.email'); // Kept name 'email' for convention or change to 'phone'
Route::get('password/otp', [App\Http\Controllers\Auth\StudentAuthController::class, 'showResetOtpForm'])->name('password.otp');
Route::post('password/otp', [App\Http\Controllers\Auth\StudentAuthController::class, 'verifyResetOtp'])->name('password.verify');
Route::get('password/reset/form', [App\Http\Controllers\Auth\StudentAuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('password/reset/update', [App\Http\Controllers\Auth\StudentAuthController::class, 'resetPassword'])->name('password.update');

// Login
Route::get('student/login', [App\Http\Controllers\Auth\StudentAuthController::class, 'showLoginForm'])->name('student.login');
Route::post('student/login', [App\Http\Controllers\Auth\StudentAuthController::class, 'login']);
Route::post('student/logout', [App\Http\Controllers\Auth\StudentAuthController::class, 'logout'])->name('student.logout');

// Register Flow
Route::get('register', [App\Http\Controllers\Auth\StudentAuthController::class, 'showRegisterForm'])->name('student.register'); // Step 1: Phone
Route::post('register/phone', [App\Http\Controllers\Auth\StudentAuthController::class, 'processPhone'])->name('student.register.phone');
Route::get('register/otp', [App\Http\Controllers\Auth\StudentAuthController::class, 'showOtpForm'])->name('student.register.otp'); // Step 2: OTP
Route::post('register/otp', [App\Http\Controllers\Auth\StudentAuthController::class, 'verifyOtp'])->name('student.register.verify');
Route::get('register/complete', [App\Http\Controllers\Auth\StudentAuthController::class, 'showCompleteForm'])->name('student.register.complete'); // Step 3: Details
Route::post('register/complete', [App\Http\Controllers\Auth\StudentAuthController::class, 'completeRegistration'])->name('student.register.finish');

// Shared Logout (fallback)
Route::post('logout', [App\Http\Controllers\Auth\AdminLoginController::class, 'logout'])->name('logout');

// Main Redirect
Route::get('/', function () {
    return redirect()->route('student.login'); // Default to student login
});
Route::get('/home', function() {
    return redirect('/');
});

// --- ADMIN ROUTES ---
Route::middleware(['auth', 'verified', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Modules
    Route::resource('courses', AdminCourseController::class);
    Route::resource('students', AdminStudentController::class);
    Route::resource('payments', AdminPaymentController::class)->only(['index', 'update']); // update for approve/reject
    Route::resource('exams', AdminExamController::class);
    Route::resource('notices', AdminNoticeController::class);
    Route::resource('enrollments', \App\Http\Controllers\Admin\EnrollmentController::class);

    // Specific actions
    // Tute Delivery (Placed here correct group)
    Route::get('tute-delivery', [App\Http\Controllers\Admin\TuteDeliveryController::class, 'index'])->name('tute-delivery.index');
    Route::patch('tute-delivery/{payment}', [App\Http\Controllers\Admin\TuteDeliveryController::class, 'updateStatus'])->name('tute-delivery.update');
    Route::post('courses/{course}/tutes', [AdminCourseController::class, 'storeTute'])->name('courses.tutes.store');
    Route::delete('tutes/{tute}', [AdminCourseController::class, 'destroyTute'])->name('tutes.destroy');
    
    Route::get('exams/{exam}/questions/create', [AdminExamController::class, 'createQuestion'])->name('exams.questions.create');
    Route::post('exams/{exam}/questions', [AdminExamController::class, 'storeQuestion'])->name('exams.questions.store');
    Route::delete('questions/{question}', [AdminExamController::class, 'destroyQuestion'])->name('questions.destroy');

    // Results Module
    Route::get('results', [App\Http\Controllers\Admin\ResultController::class, 'index'])->name('results.index');

});

// --- STUDENT ROUTES ---
Route::middleware(['auth', 'verified', 'is_student'])->prefix('student')->name('student.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

    // Courses
    Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses.index'); // All courses
    Route::get('/my-courses', [StudentCourseController::class, 'myCourses'])->name('courses.my'); // Enrolled
    Route::get('/courses/{course}', [StudentCourseController::class, 'show'])->name('courses.show'); // Course content
    Route::get('/courses/{course}/tutes/{tute}/lesson', [StudentCourseController::class, 'tutePayload'])->name('courses.tute.lesson');

    // Enrollment & Payment
    Route::post('/courses/{course}/enroll', [StudentCourseController::class, 'enroll'])->name('courses.enroll');
    
    // Exams
    Route::get('/exams', [StudentExamController::class, 'index'])->name('exams.index'); // List all
    Route::get('/exams/{exam}', [StudentExamController::class, 'show'])->name('exams.show'); // Instructions
    Route::post('/exams/{exam}/start', [StudentExamController::class, 'start'])->name('exams.start');
    Route::get('/exams/{exam}/attempt/{attempt}', [StudentExamController::class, 'attempt'])->name('exams.attempt'); // Taking it
    Route::post('/exams/{exam}/attempt/{attempt}/submit', [StudentExamController::class, 'submit'])->name('exams.submit');
    // Study Time & Analytics
    Route::get('/study-time', [App\Http\Controllers\Student\StudentStudyTimeController::class, 'index'])->name('study-time.index');
    Route::post('/study-time/heartbeat', [App\Http\Controllers\Student\StudentStudyTimeController::class, 'heartbeat'])->name('study-time.heartbeat');

    // Biology AI Chatbot
    Route::get('/biology-ai', [App\Http\Controllers\Student\BiologyAIController::class, 'index'])->name('biology-ai');
    Route::post('/biology-ai/ask', [App\Http\Controllers\Student\BiologyAIController::class, 'ask'])->name('biology-ai.ask');

    // My Marks Analyst
    Route::get('/marks-analyst', [App\Http\Controllers\Student\MarksAnalystController::class, 'index'])->name('marks-analyst.index');
    Route::post('/marks-analyst/store', [App\Http\Controllers\Student\MarksAnalystController::class, 'store'])->name('marks-analyst.store');
    Route::put('/marks-analyst/{id}', [App\Http\Controllers\Student\MarksAnalystController::class, 'update'])->name('marks-analyst.update');
    Route::delete('/marks-analyst/{id}', [App\Http\Controllers\Student\MarksAnalystController::class, 'destroy'])->name('marks-analyst.destroy');

    Route::get('/exams/{exam}/result/{attempt}', [StudentExamController::class, 'result'])->name('exams.result');

});

// Shared Profile Management
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
