<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'thumbnail',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    // --- Relationships ---

    // Generic Enrollments
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    // Students enrolled
    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments');
    }

    // Tutes / Materials
    public function tutes()
    {
        return $this->hasMany(Tute::class)->orderBy('order', 'asc');
    }

    // Notices
    public function notices()
    {
        return $this->hasMany(Notice::class);
    }

    // Exams
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}
