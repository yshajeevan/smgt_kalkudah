<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = ['name', 'year'];

    public function marks() {
        return $this->hasMany(Mark::class);
    }
}
