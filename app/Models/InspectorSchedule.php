<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspectorSchedule extends Model
{
    protected $fillable = [
        'user_id',
        'available_date',
        'status'
    ];

    public function inspector()
    {
        return $this->belongsTo(User::class, 'user_id')
                    ->where('use_type', 'Inspector');
    }

    public function inspection(){
        return $this->hasOne(Inspection::class);
    }
}
