<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'mobile_no' => ['required', 'string', 'max:20'],
            'experience' => ['nullable', 'numeric', 'min:0'],
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
            'city' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'aadhar_card' => ['required', 'string', 'max:20'],
            'pan_card' => ['required', 'string', 'max:20'],
            'bank_account' => ['required', 'string', 'max:50'],
            'ifsc_code' => ['required', 'string', 'max:20'],
            'designation' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', 'min:8'], // optional new password
        ];
    }
}
