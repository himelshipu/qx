<?php

namespace App\Http\Requests\Backend\Permission;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'description' => ['nullable', 'string'],
            'module' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }
}
