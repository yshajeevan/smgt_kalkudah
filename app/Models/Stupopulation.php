<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stupopulation extends Model
{
    use HasFactory;

    public function institute()
    {  
    return $this->belongsTo('App\Models\Institute');  
    }
}


