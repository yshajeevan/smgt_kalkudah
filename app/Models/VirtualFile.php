<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualFile extends Model
{
    use HasFactory;

    protected $primaryKey='id';  

    protected $fillable = ['employee_id','nicf','nicb','birthcert','firstappltr','promoltr','	firstdtyassm',
    'appltrcserv','designationltr','dtysssmprinst','hiqualif','appsub'];

    public function employee()  
    {  
      return $this->belongsTo('App\Models\Employee');  
    } 
}
