<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Banner
 * @mixin Model
 */
class Upload extends Model
{

    protected $table = 'uploads';

    protected $guarded = ['id'];

    static public $rules = [
        'name'        => 'required|min:3|max:191|unique:uploads,name',
        'fileToUpload' => 'required',
        'description' => 'required|max:191',
        'released_year' => 'required|date_format:Y',
        'releasedby'   => 'required|max:191',
        'relatedto'   => 'required|max:191',
        'type'   => 'required|max:60',
    ];

    /**
     * Get all the rows as an array (ready for dropdowns)
     *
     * @return array
     */
    public static function getAllList()
    {
        return self::orderBy('name')->get()->pluck('name', 'id')->toArray();
    }

}
