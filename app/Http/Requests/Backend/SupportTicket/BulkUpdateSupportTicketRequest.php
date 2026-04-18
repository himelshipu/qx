<?php

declare (strict_types = 1);

namespace App\Http\Requests\Backend\SupportTicket;

use Illuminate\Foundation\Http\FormRequest;

final class BulkUpdateSupportTicketRequest extends FormRequest
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
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:support_tickets,id'],
            'status' => ['nullable', 'in:open,in_progress,waiting_user,resolved,closed'],
            'priority' => ['nullable', 'in:low,medium,high,urgent'],
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
