<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicalExamMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_name',
        'mcq_marks',
        'essay_marks',
        'marks_obtained',
        'total_marks',
        'exam_date',
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
