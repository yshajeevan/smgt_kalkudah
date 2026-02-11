<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OlSubjectResult extends Model
{
    protected $table = 'ol_subject_results';

    protected $fillable = [
        'institute_id', 'subject', 'year', 'pass_percent', 'pi'
    ];

    public function institute()
    {
        return $this->belongsTo(SmgtInstitute::class, 'institute_id');
    }
}
