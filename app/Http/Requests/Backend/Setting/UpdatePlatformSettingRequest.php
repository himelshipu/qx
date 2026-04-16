<?php

namespace App\Http\Requests\Backend\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlatformSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('settings.update');
    }

    public function rules(): array
    {
        return [
            'charge_type' => [
                'required',
                'in:' . implode(',', config('settings.platform.charge_type.allowed')),
            ],
            'charge_value' => [
                'required',
                'numeric',
                'min:' . config('settings.platform.charge_value.min'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'charge_type.required' => 'Charge type is required.',
            'charge_type.in' => 'Charge type must be one of: ' . implode(', ', config('settings.platform.charge_type.allowed')),
            'charge_value.required' => 'Charge value is required.',
            'charge_value.numeric' => 'Charge value must be a number.',
            'charge_value.min' => 'Charge value cannot be less than ' . config('settings.platform.charge_value.min') . '.',
        ];
    }
}
