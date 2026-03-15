<?php

namespace App\Http\Requests\Backend\Moderator;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateModeratorRequest extends FormRequest
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
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $moderator = $this->route('moderator');
        $ignoreId  = $moderator instanceof User ? $moderator->id : null;

        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($ignoreId)
            ],
            'phone'     => ['nullable', 'string', 'max:30'],
            'password'  => ['nullable', 'confirmed', Password::defaults()],
            'is_active' => ['sometimes', 'boolean']
        ];
    }
}
