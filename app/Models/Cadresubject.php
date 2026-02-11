<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cadresubject extends Model
{
    use HasFactory;

    protected $fillable = ['cadre', 'cadre_code', 'category', 'subject_number', 'category2', 'app_cadre'];

    public function cfactivity()  
    {  
        return $this->hasMany('App\Models\Cfactivity');  
    }
    public function employee()  
    {  
        return $this->hasMany('App\Models\Employee');  
    }
    public function questions()
    {
        return $this->hasMany(Question::class, 'subject_id');
    }
}
