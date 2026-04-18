<?php

declare (strict_types = 1);

namespace App\Http\Requests\Backend\Conversation;

use Illuminate\Foundation\Http\FormRequest;

final class StoreConversationMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string,mixed>
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'min:1', 'max:5000'],
        ];
    }
}
