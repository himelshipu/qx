<?php

declare (strict_types = 1);

namespace App\Http\Requests\Backend\SupportTicket;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateSupportTicketRequest extends FormRequest
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
            'status' => ['required', 'in:open,in_progress,waiting_user,resolved,closed'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
            'resolved_at' => ['nullable', 'date'],
            'closed_at' => ['nullable', 'date'],
        ];
    }
}
