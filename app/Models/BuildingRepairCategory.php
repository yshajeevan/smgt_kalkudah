<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingRepairCategory extends Model
{
    use HasFactory;

    // Define the table name if it doesn't follow Laravel's convention
    protected $table = 'building_repair_categories';

    // Specify fillable fields
    protected $fillable = [
        'name',
    ];

    /**
     * Relationship: Has many building repairs.
     */
    public function repairs()
    {
        return $this->hasMany(BuildingRepair::class, 'repair_category_id');
    }
}