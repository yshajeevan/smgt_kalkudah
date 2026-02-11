<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competency extends Model
{
    use HasFactory;

    protected $table = 'competencies';

    protected $fillable = [
        'name', // Grade name
        'subject_id',
    ];

    public function subject()
    {
        return $this->belongsTo(Cadresubject::class, 'subject_id');
    }
}
