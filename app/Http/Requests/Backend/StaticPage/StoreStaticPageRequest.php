<?php

declare(strict_types=1);

namespace App\Http\Requests\Backend\StaticPage;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaticPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string,mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:'.(int) config('static-page.title_max', 255), 'unique:static_pages,title'],
            'slug' => ['nullable', 'string', 'max:'.(int) config('static-page.slug_max', 255), 'unique:static_pages,slug'],
            'content' => ['required', 'string'],
            'meta_description' => ['nullable', 'string', 'max:'.(int) config('static-page.meta_description_max', 500)],
            'meta_keywords' => ['nullable', 'string', 'max:'.(int) config('static-page.meta_keywords_max', 500)],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'show_on_footer' => ['nullable', 'boolean'],
        ];
    }
}
