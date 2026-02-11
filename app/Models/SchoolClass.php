<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SchoolClass extends Model
{
    use HasFactory;
    
    protected $table = 'school_classes';

    protected $fillable = ['id','institute_id','grade_id','prlclass','employee_id'];

    public function grade()  
    {  
      return $this->belongsTo('App\Models\Grade','grade_id');  
    }
      
    public function institute()  
    {  
      return $this->belongsTo('App\Models\Institute','institute_id');  
    }

    public function students()
    {
      return $this->hasMany(Student::class, 'grade_id', 'id');
    }

}
