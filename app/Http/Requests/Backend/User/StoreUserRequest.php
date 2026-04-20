<?php

namespace App\Http\Requests\Backend\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address_line' => ['nullable', 'string', 'max:500'],
            'profile_image_path' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'cover_image_path' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ];
    }
}