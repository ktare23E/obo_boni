<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequirementSample extends Model
{
    protected $fillable = [ 
        'requirement_id',
        'requirement_sample'
    ];

    public function requirement(){
        return $this->belongsTo(Requirement::class);
    }
}
