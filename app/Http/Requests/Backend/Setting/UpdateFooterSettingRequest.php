<?php

namespace App\Http\Requests\Backend\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFooterSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('settings.update');
    }

    public function rules(): array
    {
        return [
            'footer_pages' => [
                'nullable',
                'array',
                'max:' . config('settings.footer.max_pages'),
            ],
            'footer_pages.*' => [
                'exists:static_pages,id',
            ],
            'footer_pages_order' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'footer_pages.array' => 'Footer pages must be an array.',
            'footer_pages.max' => 'Cannot exceed ' . config('settings.footer.max_pages') . ' footer pages.',
            'footer_pages.*.exists' => 'One or more selected pages do not exist.',
        ];
    }
}
