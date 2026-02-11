<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_id','employee_id','transfer_from','transfer_to','transfer_type','letter_date','effect_from','is_approved','is_printed'
    ];

    public function process()
    {  
        return $this->belongsTo('App\Models\Process','process_id');  
    }
    public function employee()
    {  
        return $this->belongsTo('App\Models\Employee','employee_id');  
    }
    public function institute()
    {  
        return $this->belongsTo('App\Models\Institute','transfer_to');  
    }
    public function institute1()
    {  
        return $this->belongsTo('App\Models\Institute','transfer_from');  
    }

}
