<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
             'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone' => 'required',
            'address' => 'nullable|string|max:255',
            'google_id' => 'nullable|string',
            'ktp_number' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'password' => 'nullable|string|min:8|confirmed',
            'gender' => 'nullable|string',
        ];
    }
}
