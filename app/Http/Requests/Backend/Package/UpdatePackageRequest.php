<?php

namespace App\Http\Requests\Backend\Package;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackageRequest extends FormRequest
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
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'platform'           => ['required', 'string', 'in:instagram,tiktok,youtube,ugc,other'],
            'name'               => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string'],
            'base_price'         => ['required', 'numeric', 'min:0'],
            'currency'           => ['required', 'string', 'size:3', 'regex:/^[A-Za-z]{3}$/'],
            'delivery_days'      => ['nullable', 'integer', 'min:1', 'max:65535'],
            'revisions_included' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_active'          => ['sometimes', 'boolean']
        ];
    }
}
