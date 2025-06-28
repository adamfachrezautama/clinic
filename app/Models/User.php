<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, HasApiTokens, HasRoles;

    public function canAccessPanel(Panel $panel): bool
    {
       if($panel->getId() === 'admin') {

            return str_ends_with($this->email, 'admin@mail.com');
        }

        return true;
    }

     public function getDefaultGuardName(): string
    {
        return 'api';
    }



    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */



    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'role_id',
        'google_id',
        'ktp_number',
        'birth_date',
        'gender',
        'phone',
        'address',
        'certification',
        'telemedicine_fee',
        'photo',
        'chat_fee',
        'start_time',
        'end_time',
        'clinic_id',
        'specialization_id',
        'registration_type',
        'status_verified',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id','id');
    }
    public function specialization()
    {
        return $this->belongsTo(Specialization::class, 'specialization_id', 'id');
    }

}
