<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpQualification extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'course_name',
        'institution',
        'duration',
    ];
    
    public function employee()  
      {  
        return $this->belongsTo('App\Models\Employee','employee_id');  
      }
}
