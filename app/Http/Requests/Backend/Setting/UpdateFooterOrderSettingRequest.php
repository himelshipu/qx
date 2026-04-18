<?php

namespace App\Http\Requests\Backend\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFooterOrderSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('settings.update') ?? false;
    }

    /**
     * @return array<string,mixed>
     */
    public function rules(): array
    {
        return [
            'order' => ['required', 'array', 'max:' . config('settings.footer.max_pages')],
            'order.*' => ['integer', 'exists:static_pages,id'],
        ];
    }
}
