<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRecord extends Model
{
    protected $fillable = [
        'employee_id','leave_type','from_date','to_date',
        'days','leave_note','year'
    ];
}