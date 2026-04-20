<?php

namespace App\Http\Requests\Backend\CaseStudy;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCaseStudyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $maxCoverKb = (int) config('case-study.cover_image_max_kb', 5120);

        return [
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:' . $maxCoverKb],
            'external_url' => ['nullable', 'url'],
            'is_published' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
