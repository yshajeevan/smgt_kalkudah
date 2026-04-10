<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Institute;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'institute_id',
        'principal',

        // Grade 1-5
        'tot_1_5', 'pr_1_5',

        // Grade 6-9
        'tot_6_9', 'pr_6_9',

        // Grade 10-11
        'tot_10_11', 'pr_10_11',

        // A/L 1st
        'tot_arts_1st', 'pr_arts_1st',
        'tot_com_1st', 'pr_com_1st',
        'tot_physc_1st', 'pr_physc_1st',
        'tot_biosc_1st', 'pr_biosc_1st',
        'tot_etech_1st', 'pr_etech_1st',
        'tot_btech_1st', 'pr_btech_1st',

        // A/L 2nd
        'tot_arts_2nd', 'pr_arts_2nd',
        'tot_com_2nd', 'pr_com_2nd',
        'tot_physc_2nd', 'pr_physc_2nd',
        'tot_biosc_2nd', 'pr_biosc_2nd',
        'tot_etech_2nd', 'pr_etech_2nd',
        'tot_btech_2nd', 'pr_btech_2nd',

        // Teachers
        'tottea', 'prtea',

        'updated_by'
    ];

    public function institute()
    {
        return $this->belongsTo(Institute::class, 'institute_id');
    }
}
