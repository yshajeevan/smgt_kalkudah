<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingRepair extends Model
{
    use HasFactory;

    // Define the table name if it doesn't follow Laravel's convention
    protected $table = 'building_repairs';

    // Specify fillable fields
    protected $fillable = [
        'building_id',
        'building_repair_category_id',
        'description',
        'cost',
    ];

    /**
     * Relationship: Belongs to a building.
     */
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Relationship: Belongs to a repair category.
     */
    public function repairCategory()
    {
        return $this->belongsTo(BuildingRepairCategory::class, 'repair_category_id');
    }
}