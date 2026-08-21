<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    protected $fillable = [
        'business_id',
        'user_id',
        'inspector_schedule_id',
        'status',
        'remarks'
    ];

    public function business(){
        return $this->belongsTo(Business::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function schedule(){
        return $this->belongsTo(InspectorSchedule::class,'inspector_schedule_id');
    }
}
