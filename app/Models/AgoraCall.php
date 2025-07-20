<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgoraCall extends Model
{
    protected $table = 'agoracalls';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $casts = [
        'opening_time' => 'datetime',
        'closing_time' => 'datetime',
    ];

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'channel_name',
        'call_id',
    ];
}
