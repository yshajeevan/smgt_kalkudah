<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activitylog extends Model
{
    protected $table = 'activity_log';
    
    use HasFactory;

    public function activityreadlog()  
    {  
        return $this->hasMany('App\Models\Activityreadlog');  
    }
    
}
