<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'clinic' => $this->clinic,
            'specialization' => $this->specialization,
            'photo' => $this->photo,
            'status' => $this->status,
        ];
    }
}
