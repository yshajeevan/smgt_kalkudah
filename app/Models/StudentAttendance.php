<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasFactory;
    protected $table = 'student_attendances';  

    protected $primaryKey='id';  

    protected $fillable = ['student_id', 'exam_id', 'attendance'];

    public function exam()
    {  
        return $this->belongsTo('App\Models\Exam');  
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
}
