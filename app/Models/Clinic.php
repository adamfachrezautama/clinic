<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    //
    protected $table = 'clinics';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $casts = [
        'opening_time' => 'datetime',
        'closing_time' => 'datetime',
    ];
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'opening_time',
        'closing_time',
        'description',
        'spesialis'
    ];
}
