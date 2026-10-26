<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tute extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'title', 'description', 
        'type', 'file_path', 'video_url', 
        'is_published', 'order'
    ];

    protected $casts = [
        'course_id' => 'integer',
        'is_published' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
