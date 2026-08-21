<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    protected $fillable = [
        'title',
        'description',
        'file_type_allowed',
        'is_required',
        'requirement_type',
    ];

    public function submissions(){
        return $this->hasMany(RequirementSubmission::class);
    }

    public function samples(){
        return $this->hasMany(RequirementSample::class);
    }
}
