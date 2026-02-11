<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpService extends Model
{
    use HasFactory;

    public function employee()  
    {  
      return $this->hasMany('App\Models\Employee');  
    }
}

