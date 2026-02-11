<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id','name','supportive_doc','remarks'];

    public function service()
    {  
        return $this->belongsTo('App\Models\service');  
    }
}
