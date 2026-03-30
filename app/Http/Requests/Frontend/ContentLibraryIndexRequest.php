<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class ContentLibraryIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'string', 'in:all,pending,accepted,in_progress,delivered,completed,cancelled,refunded'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:50'],
        ];
    }

    public function search(): string
    {
        return trim((string) $this->string('q', ''));
    }

    public function status(): string
    {
        $status = (string) $this->string('status', 'all');

        return $status === '' ? 'all' : $status;
    }

    public function perPage(): int
    {
        return (int) $this->integer('per_page', 10);
    }
}

