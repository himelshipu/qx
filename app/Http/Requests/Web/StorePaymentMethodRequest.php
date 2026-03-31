<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentMethodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // User can only add payment methods to their own account
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Stripe payment method token (NOT the card number or CVV)
            'stripe_payment_method_id' => ['required', 'string', 'starts_with:pm_', 'max:255'],
            
            // Default card selection
            'is_default' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'stripe_payment_method_id.required' => 'Card validation failed. Please try again.',
            'stripe_payment_method_id.starts_with' => 'Invalid payment method token.',
            'stripe_payment_method_id.max' => 'Payment method token is too long.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure is_default is properly cast to boolean
        $this->merge([
            'is_default' => $this->boolean('is_default', false),
        ]);
    }
}
