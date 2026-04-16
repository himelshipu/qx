<?php

namespace App\Http\Requests\Backend\Permission;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
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
        $permissionId = (int) $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permissionId],
            'description' => ['nullable', 'string'],
            'module' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }
}
