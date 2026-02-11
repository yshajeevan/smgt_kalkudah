<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institute extends Model
{
    use HasFactory;

    protected $fillable = ['pfclerk_id', 'acctclerk_id'];

    public function employee()
    {
        return $this->hasMany('App\Models\Employee');
    }

    public function attendance()
    {
        return $this->hasMany('App\Models\Attendance');
    }

    public function stupopulation()
    {
        return $this->hasMany('App\Models\Stupopulation');
    }

    public function appcadre()
    {
        return $this->hasOne('App\Models\Appcadre');
    }

    public function pfclerk()
    {
        return $this->belongsTo('App\Models\User', 'pfclerk_id');
    }

    public function acctclerk()
    {
        return $this->belongsTo('App\Models\User', 'acctclerk_id');
    }
}
