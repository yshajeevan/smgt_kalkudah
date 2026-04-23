<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $fillable = ['nic','name','mobile','is_internal'];

    public function tokens()
    {
        return $this->hasMany(Token::class);
    }
}
