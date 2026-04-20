<?php

declare(strict_types=1);

namespace App\Http\Requests\Backend\Blog;

use App\Models\BlogPost;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var BlogPost $blogPost */
        $blogPost = $this->route('blogPost');

        return [
            'title' => [
                'required',
                'string',
                'max:'.(int) config('blog.title_max', 255),
                Rule::unique('blog_posts', 'title')->ignore($blogPost->id),
            ],
            'slug' => [
                'nullable',
                'string',
                'max:'.(int) config('blog.slug_max', 255),
                Rule::unique('blog_posts', 'slug')->ignore($blogPost->id),
            ],
            'excerpt' => ['nullable', 'string', 'max:'.(int) config('blog.excerpt_max', 1000)],
            'content' => ['required', 'string'],
            'featured_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'meta_description' => ['nullable', 'string', 'max:'.(int) config('blog.meta_description_max', 500)],
            'meta_keywords' => ['nullable', 'string', 'max:'.(int) config('blog.meta_keywords_max', 500)],
            'is_published' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
