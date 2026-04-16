<?php

namespace App\Http\Requests\Backend\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        /** @var User $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address_line' => ['nullable', 'string', 'max:500'],
            'profile_image_path' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'cover_image_path' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
        ];
    }
}