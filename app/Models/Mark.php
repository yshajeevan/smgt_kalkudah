<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    protected $fillable = ['student_id', 'subject_id', 'exam_id', 'mark', 'is_absent'];

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function subject() {
        return $this->belongsTo(Cadresubject::class);
    }

    public function exam() {
        return $this->belongsTo(Exam::class);
    }
}
