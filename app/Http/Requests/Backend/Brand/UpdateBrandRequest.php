<?php

namespace App\Http\Requests\Backend\Brand;

use App\Models\Brand;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateBrandRequest extends FormRequest
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
        $brand        = $this->route('brand');
        $ignoreUserId = $brand instanceof Brand ? $brand->user_id : null;

        return [
            'contact_name'       => ['required', 'string', 'max:255'],
            'brand_name'         => ['required', 'string', 'max:255'],
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
            'description'        => ['nullable', 'string'],
            'industry'           => ['nullable', 'string', 'max:150'],
            'website'            => ['nullable', 'url', 'max:500'],
            'location'           => ['nullable', 'string', 'max:255'],
            'city'               => ['nullable', 'string', 'max:120'],
            'country'            => ['nullable', 'string', 'max:120'],
            'postal_code'        => ['nullable', 'string', 'max:30'],
            'profile_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif,gif', 'max:5120'],
            'cover_image_file'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif,gif', 'max:6144'],
            'is_verified'        => ['sometimes', 'boolean'],
            'is_active'          => ['sometimes', 'boolean']
        ];
    }
}
