<?php

namespace App\Http\Requests\Backend\Package;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $user      = Auth::user();
        $isCreator = $user && $user->influencer()->exists();

        // If user is not a creator, ensure created_for is provided
        // If user is a creator, remove created_for from input as it will be set automatically
        if ($isCreator) {
            $this->request->remove('created_for');
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $user      = Auth::user();
        $isCreator = $user && $user->influencer()->exists();

        $rules = [
            'platform'           => ['required', 'string', 'in:facebook,instagram,tiktok,linkedin,x,youtube,ugc,other'],
            'name'               => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string'],
            'base_price'         => ['required', 'numeric', 'min:0'],
            'currency'           => ['required', 'string', 'size:3', 'regex:/^[A-Za-z]{3}$/'],
            'delivery_days'      => ['nullable', 'integer', 'min:1', 'max:65535'],
            'revisions_included' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_active'          => ['sometimes', 'boolean']
        ];

        // Only require created_for if user is not a creator
        if (!$isCreator) {
            $rules['created_for'] = ['required', 'exists:influencers,id'];
        }

        return $rules;
    }
}
