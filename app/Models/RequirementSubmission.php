<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequirementSubmission extends Model
{
    protected $fillable = [
        'requirement_id',
        'business_id',
        'file_path',
        'status',
        'remarks'
    ];

    public function requirement(){
        return $this->belongsTo(Requirement::class);
    }

    public function business(){
        return $this->belongsTo(Business::class);
    }
}
