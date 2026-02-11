<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    protected $fillable = [
        'institute_id',
        'name',
        'size',
        'building_category_id',
        'building_type_id',
        'usage',
        'constructed_on',
    ];
    public function institute()
    {
        return $this->belongsTo(Institute::class, 'institute_id');
    }

    public function category()
    {
        return $this->belongsTo(BuildingCategory::class, 'building_category_id');
    }

    public function type()
    {
        return $this->belongsTo(BuildingType::class, 'building_type_id');
    }
    
    public function repairs()
    {
        return $this->hasMany(BuildingRepair::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
