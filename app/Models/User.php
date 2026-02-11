<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'otp', 'otp_expires_at', 'password','institute_id','device_key','is_active','photo','phone','desig','employee_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function process()  
    {  
        return $this->hasMany('App\Models\Process','user_id');  
    }
    public function service()  
    {  
        return $this->hasMany('App\Models\Service');  
    }
    public function message()  
    {  
        return $this->hasMany('App\Models\Message');  
    }
    public function institute()  
    {  
        return $this->hasMany('App\Models\Institute');  
    }
    public function employee()  
    {  
        return $this->belongsTo('App\Models\Employee','employee_id','id');   
    }
    public function servicelog()  
    {  
        return $this->hasMany('App\Models\ServiceLog');  
    }
}
