<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'designation',
        'branch',
        'email',
        'phone',
        'whatsapp',
        'image',
        'is_website',
        'list_order',
    ];

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }
}
