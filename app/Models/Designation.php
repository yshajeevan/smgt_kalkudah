<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = ['designation', 'app_cadre', 'catg'];

    public function employee()  
    {  
        return $this->hasMany('App\Models\Employee');  
    }
    
}

