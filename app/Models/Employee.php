<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Employee extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
      'empno',
      'nic',
      'nicnew',
      'title',
      'name_with_initial_e',
      'name_denoted_by_initial_e',
      'name_with_initial_t',
      'name_denoted_by_initial_t',
      'designation_id',
      'empservice_id',
      'dsdivision_id',
      'cadresubject_id',
      'cadresubject1_id',
      'cadresubject2_id',
      'cadresubject3_id',
      'degree_id',
      'degsubject1_id',
      'degsubject2_id',
      'degsubject3_id',
      'dob',
      'gender',
      'peraddress',
      'status',
      'ethinicity',
      'trained',
      'mobile',
      'whatsapp',
      'email',
      'grade',
      'deginstitute_id',
      'degtype',
      'institute_id',
      'institute1_id',
      'current_working_station',
      'dtyasmfapp',
      'dtyasmcser',
      'dtyasmprins',
      'appcategory_id',
      'appsubject',
      'fixedphone',
      'tmpaddress',
      'remark',
      'zone_id',
      'religion',
      'civilstatus',
      'highqualification_id',
      'gndivision_id',
      'transmode_id',
      'distores',
      'device_key',
      'isUser',
      'mobile_vertified_at',
    ];   
    protected $guarded = [ "id" ];

    public function scopeLastSync($query)
    {
          return $query->select(
              DB::raw('CONCAT(title, ". ", name_with_initial_e) as namewithinitial'),
              'employees.*'
          );

    }

    public function process()  
      {  
          return $this->hasMany('App\Models\Process','employee_id');  
      }
      
    public function institute()  
      {  
        return $this->belongsTo('App\Models\Institute');  
      } 
      
    public function institute1()
        {  
            return $this->belongsTo('App\Models\Institute','institute1_id');  
        }

    public function workingStation()
        {  
            return $this->belongsTo('App\Models\Institute','current_working_station');  
        }
        
        
    public function user()  
      {  
        return $this->hasOne('App\Models\User');  
      } 
      
    public function designation()  
      {  
        return $this->belongsTo('App\Models\Designation');  
      }
      
    public function empservice()  
      {  
        return $this->belongsTo('App\Models\EmpService');  
      }
      
    public function gndivision()  
      {  
        return $this->belongsTo('App\Models\GnDivision');  
      }
      
    public function dsdivision()  
      {  
        return $this->belongsTo('App\Models\DsDivision');  
      }
      
    public function zone()  
      {  
        return $this->belongsTo('App\Models\Zone');  
      }
      
    public function degree()  
      {  
        return $this->belongsTo('App\Models\Degree');  
      }
      
    public function degsubject1()  
      {  
        return $this->belongsTo('App\Models\DegSubject','degsubject1_id');  
      }
      
    public function degsubject2()  
      {  
        return $this->belongsTo('App\Models\DegSubject','degsubject2_id');  
      }
      
    public function degsubject3()  
      {  
        return $this->belongsTo('App\Models\DegSubject','degsubject3_id');  
      }
      
    public function appcategory()  
      {  
        return $this->belongsTo('App\Models\AppCategory');  
      }
      
    public function cadresubject()  
      {  
        return $this->belongsTo('App\Models\Cadresubject');  
      }
      
    public function cadresubject1()  
      {  
        return $this->belongsTo('App\Models\Cadresubject','cadresubject1_id');  
      }
      
    public function cadresubject2()  
      {  
        return $this->belongsTo('App\Models\Cadresubject','cadresubject2_id');  
      }
      
    public function cadresubject3()  
      {  
        return $this->belongsTo('App\Models\Cadresubject','cadresubject3_id');  
      }
      
    public function transmode()  
      {  
        return $this->belongsTo('App\Models\TransMode');  
      }
      
    public function virtualfile()  
      {  
        return $this->hasOne('App\Models\VirtualFile');  
      }
    public function servicetransfer()  
      {  
        return $this->hasOne('App\Models\ServiceTransfer')->whereIn('service_transfers.transfer_type', [0,3])->latest();  
      }
    public function servicehistory()  
      {  
        return $this->hasMany('App\Models\ServiceHistory','nic','nic');  
      }
    public function qualification()  
      {  
        return $this->hasMany('App\Models\EmpQualification');  
      }
    public function teachsubject()  
      {  
        return $this->hasMany('App\Models\EmpTeachSubject');  
      }
    public function empdummy()  
      {  
        return $this->hasOne('App\Models\EmployeeDummy');  
      }
    public function highqualification()  
      {  
        return $this->belongsTo('App\Models\HighQualification','highqualification_id');  
      }
}