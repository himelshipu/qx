<?php

namespace App\Http\Requests\Backend\FeaturedCollaboration;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeaturedCollaborationRequest extends FormRequest
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
        $maxImageKb = (int) config('featured-collaboration.image_max_kb', 5120);
        $maxVideoKb = (int) config('featured-collaboration.video_max_kb', 102400);
        $maxThumbKb = (int) config('featured-collaboration.thumbnail_max_kb', 2048);

        return [
            'brand_name' => ['required', 'string', 'max:255'],
            'asset_type' => ['required', 'in:image,video'],
            'image_path' => ['required_if:asset_type,image', 'nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:' . $maxImageKb],
            'video_path' => ['required_if:asset_type,video', 'nullable', 'file', 'mimes:mp4,webm,mov', 'max:' . $maxVideoKb],
            'thumbnail_path' => ['exclude_unless:asset_type,video', 'nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:' . $maxThumbKb],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
