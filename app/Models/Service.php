<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $primaryKey='id';  

    protected $fillable = ['service', 'branch', 'user1_id','res1time', 'user2_id','res2time', 'user3_id','res3time',
    'user4_id','res4time', 'user5_id','res5time', 'user6_id','res6time', 'user7_id','res7time', 'user8_id','res8time',
    'user9_id','res9time', 'user10_id','res10time','remarks', 'smsdesc'];

public function process()  
{  
    return $this->hasMany('App\Models\Process','service_id');  
} 
public function user1()
{  
    return $this->belongsTo('App\Models\User','user1_id');  
}
public function user2()
{  
    return $this->belongsTo('App\Models\User','user2_id');  
}
public function user3()
{  
    return $this->belongsTo('App\Models\User','user3_id');  
}
public function user4()
{  
    return $this->belongsTo('App\Models\User','user4_id');  
}
public function user5()
{  
    return $this->belongsTo('App\Models\User','user5_id');  
}
public function user6()
{  
    return $this->belongsTo('App\Models\User','user6_id');  
}
public function user7()
{  
    return $this->belongsTo('App\Models\User','user7_id');  
}
public function user8()
{  
    return $this->belongsTo('App\Models\User','user8_id');  
}
public function user9()
{  
    return $this->belongsTo('App\Models\User','user9_id');  
}
public function user10()
{  
    return $this->belongsTo('App\Models\User','user10_id');  
}
 public function servicetype1()
{  
    return $this->belongsTo('App\Models\ServiceType','servicetype1_id');  
}
public function servicetype2()
{  
    return $this->belongsTo('App\Models\ServiceType','servicetype2_id');  
}
public function servicetype3()
{  
    return $this->belongsTo('App\Models\ServiceType','servicetype3_id');  
}
public function servicetype4()
{  
    return $this->belongsTo('App\Models\ServiceType','servicetype4_id');  
}
public function servicetype5()
{  
    return $this->belongsTo('App\Models\ServiceType','servicetype5_id');  
}
public function servicetype6()
{  
    return $this->belongsTo('App\Models\ServiceType','servicetype6_id');  
}
public function servicetype7()
{  
    return $this->belongsTo('App\Models\ServiceType','servicetype7_id');  
}
public function servicetype8()
{  
    return $this->belongsTo('App\Models\ServiceType','servicetype8_id');  
}
public function servicetype9()
{  
    return $this->belongsTo('App\Models\ServiceType','servicetype9_id');  
}
public function servicetype10()
{  
    return $this->belongsTo('App\Models\ServiceType','servicetype10_id');  
}
}
