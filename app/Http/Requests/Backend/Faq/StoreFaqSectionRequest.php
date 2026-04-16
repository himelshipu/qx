<?php

namespace App\Http\Requests\Backend\Faq;

use Illuminate\Foundation\Http\FormRequest;

class StoreFaqSectionRequest extends FormRequest
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
            'section_code' => ['required', 'string', 'max:120', 'unique:faq_sections,section_code'],
            'section_title' => ['required', 'string', 'max:255'],
            'audience_type' => ['required', 'in:all,brand,influencer'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}