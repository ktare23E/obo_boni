<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $fillable = [
        'user_id',
        'business_name',
        'address',
        'type_of_business',
        'registration_no',
        'status',
        'approved_date',
        'image_path',
        'remarks',
        'place_description',
        'lat',
        'lng',
        'building_type'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    
    public function submissions(){
        return $this->hasMany(RequirementSubmission::class);
    }

    public function inspection(){
        return $this->hasOne(Inspection::class);
    }

    public function permit(){
        return $this->hasOne(Permit::class);
    }
}
