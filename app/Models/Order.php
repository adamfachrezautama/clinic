<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $table = 'orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

     protected $primaryKey = 'id';
    public $timestamps = true;
    public $incrementing = true;

   protected $fillable = [
        'patient_id',
        'doctor_id',
        'service',
        'price',
        'payment_url',
        'status',
        'duration',
        'clinic_id',
        'start_time',
        'end_time',
        'status_service',
        'external_id',
    ];

    protected $casts = [
    'start_time' => 'datetime',
    'end_time'   => 'datetime',
];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
