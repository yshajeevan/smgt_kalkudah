<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentResponse extends Model
{
    use HasFactory;

    protected $table = 'student_responses';

    protected $fillable = [
        'student_id',
        'question_id',
        'is_correct',        // for MCQ
        'obtained_marks',    // for SAQ
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // Accessors for convenience
    public function getPercentageAttribute()
    {
        if ($this->max_marks && $this->obtained_marks !== null) {
            return round(($this->obtained_marks / $this->max_marks) * 100, 2);
        }
        return null;
    }
}
