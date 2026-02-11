<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceLog extends Model
{
    use HasFactory;

    public function process()  
    {  
      return $this->belongsTo('App\Models\Process');  
    }

    public function user()  
    {  
      return $this->belongsTo('App\Models\User');  
    }
}


