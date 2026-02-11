<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfidSensor extends Model
{
    use HasFactory;

    protected $primaryKey='id';  

    protected $fillable = ['uid', 'time_in', 'time_out'];
}
