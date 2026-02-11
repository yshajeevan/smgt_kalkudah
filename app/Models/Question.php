<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';

    protected $fillable = [
        'syllabus_unit_id',
        'exam_id',
        'subject_id',
        'question_no',
        'type', // 'MCQ' or 'SAQ'
        'max_marks'
    ];

    public function syllabusUnit()
    {
        return $this->belongsTo(SyllabusUnit::class, 'syllabus_unit_id', 'id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

     public function subject()
    {
        return $this->belongsTo(Cadresubject::class, 'subject_id');
    }

    public function responses()
    {
        return $this->hasMany(StudentResponse::class);
    }
}
