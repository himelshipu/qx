<?php

namespace App\Http\Requests\Backend\Creator;

use App\Models\Creator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateCreatorRequest extends FormRequest
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
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $creator      = $this->route('creator');
        $ignoreUserId = $creator instanceof Creator ? $creator->user_id : null;

        return [
            'full_name'          => ['required', 'string', 'max:255'],
            'display_name'       => ['nullable', 'string', 'max:255'],
            'title_name'         => ['nullable', 'string', 'max:255'],
            'email'              => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($ignoreUserId)
            ],
            'phone'              => ['nullable', 'string', 'max:30'],
            'password'           => ['nullable', 'confirmed', Password::defaults()],
            'bio'                => ['nullable', 'string', 'max:500'],
            'audience'           => ['nullable', 'string'],
            'location'           => ['nullable', 'string', 'max:255'],
            'city'               => ['nullable', 'string', 'max:120'],
            'country'            => ['nullable', 'string', 'max:120'],
            'postal_code'        => ['nullable', 'string', 'max:30'],
            'gender'             => ['nullable', 'in:male,female,other'],
            'categories'         => ['nullable', 'array'],
            'categories.*'       => ['integer', 'exists:categories,id'],
            'profile_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif,gif', 'max:5120'],
            'cover_image_file'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif,gif', 'max:6144'],
            'is_active'          => ['sometimes', 'boolean'],
            'is_featured'        => ['sometimes', 'boolean'],
            'featured_priority'  => ['nullable', 'integer', 'min:1', 'max:999']
        ];
    }
}
