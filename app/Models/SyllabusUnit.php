<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyllabusUnit extends Model
{
    use HasFactory;

    protected $table = 'syllabus_units'; // renamed table

    protected $fillable = [
        'competency_id',
        'name',      // unit/topic name
        'code',       // e.g., S1.1
        'worksheet'
    ];

    public function competency()
    {
        return $this->belongsTo(Competency::class);
    }

    public function subject()
    {
        return $this->belongsTo(Cadresubject::class, 'subject_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'syllabus_unit_id');
    }
}
