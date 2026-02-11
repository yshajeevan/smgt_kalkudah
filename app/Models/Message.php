<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $fillable=['sender_id','reciever_id','name','message','email','phone','read_at','photo','subject','file'];

    public function sender()
    {  
        return $this->belongsTo('App\Models\User','sender_id');  
    }

}
