<?php

namespace App\Http\Requests\Backend\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('settings.update');
    }

    public function rules(): array
    {
        return [
            'mailer' => [
                'nullable',
                'string',
                'max:' . config('settings.email.mailer.max'),
            ],
            'host' => [
                'nullable',
                'string',
                'max:' . config('settings.email.host.max'),
            ],
            'port' => [
                'nullable',
                'integer',
                'min:' . config('settings.email.port.min'),
                'max:' . config('settings.email.port.max'),
            ],
            'username' => [
                'nullable',
                'string',
                'max:' . config('settings.email.username.max'),
            ],
            'password' => [
                'nullable',
                'string',
                'max:' . config('settings.email.password.max'),
            ],
            'encryption' => [
                'nullable',
                'string',
                'max:' . config('settings.email.encryption.max'),
                'in:' . implode(',', config('settings.email.encryption.allowed')),
            ],
            'from_name' => [
                'nullable',
                'string',
                'max:' . config('settings.email.from_name.max'),
            ],
            'from_address' => [
                'nullable',
                'email',
                'max:' . config('settings.email.from_address.max'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'port.integer' => 'Port must be a valid integer.',
            'port.min' => 'Port must be at least ' . config('settings.email.port.min') . '.',
            'port.max' => 'Port cannot exceed ' . config('settings.email.port.max') . '.',
            'encryption.in' => 'Encryption must be one of: ' . implode(', ', config('settings.email.encryption.allowed')),
            'from_address.email' => 'From address must be a valid email.',
        ];
    }
}
