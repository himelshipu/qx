<?php

namespace App\Http\Requests\Backend\Setting;

use App\Services\Admin\SettingService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RestoreSettingEntityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('settings.restore') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'type' => (string) $this->route('type'),
            'id' => (int) $this->route('id'),
        ]);
    }

    /**
     * @return array<string,mixed>
     */
    public function rules(): array
    {
        /** @var SettingService $service */
        $service = app(SettingService::class);
        $allowedTypes = array_keys($service->getRecoveryModelMap());

        return [
            'type' => ['required', 'string', Rule::in($allowedTypes)],
            'id' => ['required', 'integer', 'min:1'],
        ];
    }
}
