<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activityreadlog extends Model
{
    protected $table = 'activity_read_log';

    use HasFactory;

    protected $fillable = ['activity_log_id'];

    public function activitylog()  
    {  
        return $this->belongsTo('App\Models\Activitylog');  
    }
}
