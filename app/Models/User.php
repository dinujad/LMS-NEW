<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
        'student_id',
        'last_seen_at', // Activity Tracking
        // New Profile Fields
        'whatsapp_number', 'nic', 'dob',
        'school', 'exam_year', 'medium', 'stream',
        'address_line_1', 'address_line_2', 'district',
        'guardian_name', 'guardian_phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if ($user->role === 'student' && empty($user->student_id)) {
                // Get the last student created to determine the next ID
                // We parse the numeric part of the last SID
                // This is a simple implementation, concurrency might require locking but sufficient for this scope.
                
                $lastStudent = \App\Models\User::where('role', 'student')
                    ->whereNotNull('student_id')
                    ->orderBy('id', 'desc') // Assuming ID order roughly correlates, or we can use created_at
                    ->first();

                if ($lastStudent && preg_match('/SID(\d+)/', $lastStudent->student_id, $matches)) {
                    $nextNum = intval($matches[1]) + 1;
                } else {
                    $nextNum = 1000; // Start point
                }

                $user->student_id = 'SID' . $nextNum;
            }
        });
    }

    // --- Helpers ---
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    // --- Relationships ---
    
    // Enrollments (as Student)
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    // Courses enrolled in (Through enrollments)
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')
                    ->withPivot('status', 'enrolled_at')
                    ->wherePivot('status', 'active');
    }

    // Payments
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Exam Attempts
    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }
}
