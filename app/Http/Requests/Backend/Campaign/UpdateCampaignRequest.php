<?php

namespace App\Http\Requests\Backend\Campaign;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCampaignRequest extends FormRequest
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
        return [
            'title'              => ['required', 'string', 'max:255'],
            'campaign_type'      => ['required', Rule::in(['facebook', 'instagram', 'tiktok', 'linkedin', 'x', 'youtube', 'ugc', 'other'])],
            'description'        => ['nullable', 'string'],
            'instructions'       => ['nullable', 'string'],
            'status'             => ['required', Rule::in(['draft', 'published', 'paused', 'closed', 'archived'])],
            'budget_min'         => ['nullable', 'numeric', 'min:0'],
            'budget_max'         => ['nullable', 'numeric', 'min:0'],
            'currency'           => ['required', 'string', 'size:3'],
            'start_date'         => ['nullable', 'date'],
            'end_date'           => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active'          => ['sometimes', 'boolean'],
            'influencer_count'   => ['nullable', 'integer', 'min:1'],
            'target_gender'      => ['nullable', Rule::in(['any', 'male', 'female', 'other'])],
            'age_min'            => ['nullable', 'integer', 'min:13', 'max:100'],
            'age_max'            => ['nullable', 'integer', 'min:13', 'max:100'],
            'targeting_notes'    => ['nullable', 'string'],
            'categories'         => ['nullable', 'array'],
            'categories.*'       => ['integer', 'distinct', 'exists:categories,id'],
            'follower_ranges'    => ['nullable', 'array'],
            'follower_ranges.*'  => ['integer', 'distinct', 'exists:follower_ranges,id'],
            'target_countries'   => ['nullable', 'array'],
            'target_countries.*' => ['string', 'distinct', 'size:2', 'regex:/^[A-Za-z]{2}$/']
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $data = $validator->getData();

            // Check if budget_max is greater than or equal to budget_min when both are provided
            if (!empty($data['budget_min']) && !empty($data['budget_max'])) {
                $budgetMin = (float) $data['budget_min'];
                $budgetMax = (float) $data['budget_max'];

                if ($budgetMax < $budgetMin) {
                    $validator->errors()->add('budget_max', 'Maximum budget must be greater than or equal to minimum budget.');
                }
            }

            // Check if age_max is greater than or equal to age_min when both are provided
            if (!empty($data['age_min']) && !empty($data['age_max'])) {
                $ageMin = (int) $data['age_min'];
                $ageMax = (int) $data['age_max'];

                if ($ageMax < $ageMin) {
                    $validator->errors()->add('age_max', 'Maximum age must be greater than or equal to minimum age.');
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Campaign title is required.',
            'title.max' => 'Campaign title cannot exceed 255 characters.',
            'campaign_type.required' => 'Please select a campaign type.',
            'campaign_type.in' => 'Please select a valid campaign type.',
            'status.required' => 'Please select a campaign status.',
            'status.in' => 'Please select a valid campaign status.',
            'budget_min.numeric' => 'Minimum budget must be a valid number.',
            'budget_min.min' => 'Minimum budget cannot be negative.',
            'budget_max.numeric' => 'Maximum budget must be a valid number.',
            'budget_max.min' => 'Maximum budget cannot be negative.',

            'currency.required' => 'Please select a currency.',
            'currency.size' => 'Currency must be a 3-letter code (e.g., USD, EUR).',
            'start_date.date' => 'Start date must be a valid date.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after_or_equal' => 'End date must be on or after the start date.',
            'influencer_count.integer' => 'Influencer count must be a whole number.',
            'influencer_count.min' => 'Influencer count must be at least 1.',
            'age_min.integer' => 'Minimum age must be a whole number.',
            'age_min.min' => 'Minimum age must be at least 13.',
            'age_min.max' => 'Minimum age cannot exceed 100.',
            'age_max.integer' => 'Maximum age must be a whole number.',
            'age_max.min' => 'Maximum age must be at least 13.',
            'age_max.max' => 'Maximum age cannot exceed 100.',
            'age_max.gte' => 'Maximum age must be greater than or equal to minimum age.',
            'categories.*.exists' => 'One or more selected categories are invalid.',
            'categories.*.distinct' => 'Duplicate categories are not allowed.',
            'follower_ranges.*.exists' => 'One or more selected follower ranges are invalid.',
            'follower_ranges.*.distinct' => 'Duplicate follower ranges are not allowed.',
            'target_countries.*.regex' => 'Country code must be a valid 2-letter code.',
            'target_countries.*.size' => 'Country code must be exactly 2 characters.',
        ];
    }
}
