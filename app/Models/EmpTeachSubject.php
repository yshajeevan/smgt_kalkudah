<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpTeachSubject extends Model
{
    use HasFactory;

     protected $fillable = [
        'employee_id',
        'cadresubject_id',
        'periods',
    ];
    
    
    public function employee()  
      {  
        return $this->belongsTo('App\Models\Employee','employee_id');  
      }
}
