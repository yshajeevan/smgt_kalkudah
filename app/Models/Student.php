<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';
    
    protected $fillable = [
        'institute_id',
        'admission_number',
        'birth_number',
        'nic',
        'name',
        'dob',
        'gender',
        'ethnicity',
        'religion',
        'grade_id',
        'cadresubject4_id',
        'cadresubject1_id',
        'cadresubject2_id',
        'cadresubject3_id',
        'address',
        'dsdivision_id',
        'gndivision_id',
        'mobile',
        'father_name',
        'father_nic',
        'mother_name',
        'ews_color',
        'ews_updated_by',
        'status',
        'created_by',
        'updated_by',
    ];

    // relationships
    public function gradeRelation(){
        return $this->belongsTo(\App\Models\Grade::class, 'grade_id');
    }

    public function cadreSubject1() {
        return $this->belongsTo(Cadresubject::class, 'cadresubject1_id');
    }

    public function cadreSubject2() {
        return $this->belongsTo(Cadresubject::class, 'cadresubject2_id');
    }

    public function cadreSubject3() {
        return $this->belongsTo(Cadresubject::class, 'cadresubject3_id');
    }
    
    public function cadreSubject4() {
        return $this->belongsTo(Cadresubject::class, 'cadresubject4_id');
    }

    public function dsdivision() {
        return $this->belongsTo(Dsdivision::class, 'dsdivision_id');
    }

    public function gndivision() {
        return $this->belongsTo(Gndivision::class, 'gndivision_id');
    }

    public function class()  
    {  
        return $this->belongsTo('App\Models\SchoolClass','grade_id');  
    }
      
    public function institute()  
    {  
        return $this->belongsTo('App\Models\Institute','institute_id');  
    }
    public function marks()
    {
        return $this->hasMany(Mark::class, 'student_id', 'id');
    }
    public function attendance()
    {
        return $this->hasMany(StudentAttendance::class, 'student_id', 'id');
    }

}
