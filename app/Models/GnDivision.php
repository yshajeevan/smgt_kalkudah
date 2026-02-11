<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GnDivision extends Model
{
    use HasFactory;

    protected $fillable = ['dsdivision_id','gn','gpslocation'];
    
    public static function getAllList()
    {
        return self::orderBy('gn')->get()->pluck('gn','gn')->toArray();
    }
    
    public function dsdivision()
    {  
        return $this->belongsTo('App\Models\DsDivision');  
    }

}
