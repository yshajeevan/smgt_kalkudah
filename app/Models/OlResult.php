<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OlResult extends Model
{
    use HasFactory;

    public function institute()
    {
        return $this->belongsTo(SmgtInstitute::class, 'institute_id');
    }
}