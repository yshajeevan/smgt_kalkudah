<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $fillable = [
        'token_number','purpose','branch_id','visitor_id',
        'status','satisfaction','served_at'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }
}
