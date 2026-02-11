<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Banner
 * @mixin Model
 */
class Programme extends Model
{
    protected $table = 'programmes';

    protected $guarded = ['id'];

    /**
     * Validation rules for this model
     */
    static public $rules = [
        'name'        => 'required|min:3|max:191',
        'coordinator_id' => 'required|nullable|max:191',
    ];

    /**
     * Get all the rows as an array (ready for dropdowns)
     *
     * @return array
     */

    /**
     * Get the Page many to many
     */
    public function coordinator()
    {
        return $this->belongsTo(Employee::class, 'coordinator_id');
    }
}
