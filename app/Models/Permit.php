<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permit extends Model
{
    protected $fillable = [
        'business_id',
        'release_date',
        'status'
    ];

    public function business(){
        return $this->belongsTo(Business::class);
    }
}
