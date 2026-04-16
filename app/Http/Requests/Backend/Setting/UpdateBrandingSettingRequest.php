<?php

namespace App\Http\Requests\Backend\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandingSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('settings.update');
    }

    public function rules(): array
    {
        return [
            'site_name' => [
                'required',
                'string',
                'max:' . config('settings.branding.site_name.max'),
            ],
            'tagline' => [
                'nullable',
                'string',
                'max:' . config('settings.branding.tagline.max'),
            ],
            'logo_light' => [
                'nullable',
                'image',
                'mimes:' . implode(',', config('settings.branding.logo_light.mimes')),
                'max:' . config('settings.branding.logo_light.max'),
            ],
            'logo_dark' => [
                'nullable',
                'image',
                'mimes:' . implode(',', config('settings.branding.logo_dark.mimes')),
                'max:' . config('settings.branding.logo_dark.max'),
            ],
            'favicon' => [
                'nullable',
                'image',
                'mimes:' . implode(',', config('settings.branding.favicon.mimes')),
                'max:' . config('settings.branding.favicon.max'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'site_name.required' => 'Site name is required.',
            'site_name.max' => 'Site name cannot exceed ' . config('settings.branding.site_name.max') . ' characters.',
            'logo_light.image' => 'Light logo must be a valid image file.',
            'logo_light.mimes' => 'Light logo must be one of: ' . implode(', ', config('settings.branding.logo_light.mimes')),
            'logo_dark.image' => 'Dark logo must be a valid image file.',
            'logo_dark.mimes' => 'Dark logo must be one of: ' . implode(', ', config('settings.branding.logo_dark.mimes')),
            'favicon.image' => 'Favicon must be a valid image file.',
            'favicon.mimes' => 'Favicon must be one of: ' . implode(', ', config('settings.branding.favicon.mimes')),
        ];
    }
}
