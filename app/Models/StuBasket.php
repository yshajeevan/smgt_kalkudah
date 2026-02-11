<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StuBasket extends Model
{
    use HasFactory;
    
    public function cadresubject()
    {  
        return $this->belongsTo('App\Models\Cadresubject','cadresubject_id');  
    }
}
