<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class ContentLibraryIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'string', 'in:all,pending,accepted,in_progress,delivered,completed,cancelled,refunded'],
            'platform' => ['nullable', 'string', 'in:all,facebook,instagram,tiktok,linkedin,x,youtube,ugc,other'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:50'],
        ];
    }

    public function search(): string
    {
        return trim((string) $this->string('q', ''));
    }

    public function status(): string
    {
        return $this->string('status', 'all');
    }

    public function platform(): string
    {
        return $this->string('platform', 'all');
    }

    public function dateFrom(): ?string
    {
        return $this->filled('date_from') ? $this->date('date_from')->format('Y-m-d') : null;
    }

    public function dateTo(): ?string
    {
        return $this->filled('date_to') ? $this->date('date_to')->format('Y-m-d') : null;
    }

    public function perPage(): int
    {
        return (int) $this->integer('per_page', 10);
    }
}