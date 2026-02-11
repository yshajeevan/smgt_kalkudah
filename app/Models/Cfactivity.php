<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class Cfactivity extends Model
{
    use HasFactory;
    
    
    protected $fillable = ['id','cfid','thrustarea','component_id','goal_id','activity','month','objectcode_id','estimated_cost','cadresubject_id','remark',
    'expenditure','is_done','process_id'];

    // public function scopeWithTotal($query)
    // {
    //     return $query->select('*',DB::Raw('jan + feb + mar + apr + may + jun + jul + aug + sep + oct + nov + dem as totalunits'),
    //      DB::raw('((jan + feb + mar + apr + may + jun + jul + aug + sep + oct + nov + dem) * unitcost) as totalcost'),
    //      DB::raw('(expenditure / ((jan + feb + mar + apr + may + jun + jul + aug + sep + oct + nov + dem) * unitcost)) * 100 as progress'))
    //      ->with('cadresubject');
    // }

    // public function scopeWithGrand($query)
    // {
    //     return $query->select(DB::raw('sum((jan + feb + mar + apr + may + jun + jul + aug + sep + oct + nov + dem) * unitcost) as totalcost'),
    //      DB::raw('((jan + feb + mar + apr + may + jun + jul + aug + sep + oct + nov + dem) * unitcost) as expenditure'));
    // }

    // public function scopeWithGroup($query)
    // {
    //     return $query->groupBy('cadresubject_id')->select('cadresubject_id',DB::Raw("(sum(jan) + sum(feb) + sum(mar) + sum(apr) + sum(may) + sum(jun) + sum(jul) + sum(aug) + sum(sep) + sum(oct) + sum(nov) + sum(dem)) as totalunits"),
    //      DB::raw("sum((jan + feb + mar + apr + may + jun + jul + aug + sep + oct + nov + dem) * unitcost) as totalcost"),
    //      DB::raw("(sum(expenditure)/sum((jan + feb + mar + apr + may + jun + jul + aug + sep + oct + nov + dem) * unitcost)) * 100 as finprogress"),
    //      DB::raw("(sum(unitdone)/(sum(jan) + sum(feb) + sum(mar) + sum(apr) + sum(may) + sum(jun) + sum(jul) + sum(aug) + sum(sep) + sum(oct) + sum(nov) + sum(dem))) * 100 as phyprogress"),
    //      DB::raw("sum(expenditure) as totalexpenditure"),DB::raw("sum(unitdone) as totalunitsdone"))->with('cadresubject');
    // }
    
     public function scopeWithCodeGroup($query)
    {
        return $query->select('objectcode_id','estimated_cost')->groupBy('objectcode_id');
    }

    public function cadresubject()  
    {  
        return $this->belongsTo('App\Models\Cadresubject','cadresubject_id');  
    }
        
    public function objectcode()  
    {  
        return $this->belongsTo('App\Models\Cfcode','objectcode_id');  
    }
    
     public function goal()  
    {  
        return $this->belongsTo('App\Models\Cfgoal','goal_id');  
    }
}

