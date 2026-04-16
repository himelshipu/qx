<?php

namespace App\Http\Requests\Backend\KnowledgeBase;

use App\Models\KnowledgeBaseArticle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKnowledgeBaseArticleRequest extends FormRequest
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
        /** @var KnowledgeBaseArticle $article */
        $article = $this->route('article');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('knowledge_base_articles', 'slug')->ignore($article->id),
            ],
            'badge' => ['nullable', 'string', 'max:120'],
            'summary' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'read_time_minutes' => ['required', 'integer', 'min:1', 'max:60'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'published_at' => ['nullable', 'date'],
            'is_featured' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}