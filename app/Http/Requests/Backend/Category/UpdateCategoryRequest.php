<?php

namespace App\Http\Requests\Backend\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:150'],
            'slug'        => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string'],
            'icon_file'   => ['nullable', 'file', 'mimes:svg', 'max:1024'],
            'image_file'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif,gif', 'max:5120'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['sometimes', 'boolean']
        ];
    }
}
