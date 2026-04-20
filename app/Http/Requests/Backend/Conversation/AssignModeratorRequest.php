<?php

declare (strict_types = 1);

namespace App\Http\Requests\Backend\Conversation;

use Illuminate\Foundation\Http\FormRequest;

final class AssignModeratorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->user_type === 'admin';
    }

    /**
     * @return array<string,mixed>
     */
    public function rules(): array
    {
        return [
            'moderator_user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
