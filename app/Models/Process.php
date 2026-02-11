<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cfactivity;

class process extends Model
{
    use HasFactory;
    protected $primaryKey='id';  

    protected $fillable = ['process1time','process2time','process3time','process4time',
    'process5time','process6time','process7time','process8time','process9time','process10time','user_id','last_updated_user','pendingchk','despending','uniquekey','feedbackscale'];

  public function service()  
  {  
    return $this->belongsTo('App\Models\Service');  
  } 
  public function employee()  
  {  
    return $this->belongsTo('App\Models\Employee')->LastSync();  
  }
  public function user()  
  {  
    return $this->belongsTo('App\Models\User');  
  }
  public function servicelog()  
  {  
      return $this->hasMany('App\Models\ServiceLog');  
  }
  public function transfer()  
  {  
      return $this->hasOne('App\Models\ServiceTransfer','process_id');  
  }
  public function cfactivity()  
  {  
      return $this->hasOne('App\Models\Cfactivity','process_id');  
  }
  public function processorder()  
  {  
      return $this->hasMany('App\Models\TestprocessOrder');  
  }
  public function delete()
  {
    $this->servicelog()->delete();
    $this->transfer()->delete();
    Cfactivity::where('process_id', $this->id)->update(['is_done' => 0, 'process_id' => '']);
    return parent::delete();
  }
}
