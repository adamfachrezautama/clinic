<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,',
            'password' => 'sometimes|required|string|min:8',
            'role' => 'sometimes|required|string',
            'clinic_id' => 'nullable|exists:clinics,id',
            'specialization_id' => 'nullable|exists:specializations,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'certification' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'chat_fee' => 'nullable|numeric|min:0',
            'telemedicine_fee' => 'nullable|numeric|min:0',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            // 'status_verified' => 'sometimes|in:verified,pending,rejected',
        ];
    }
}
