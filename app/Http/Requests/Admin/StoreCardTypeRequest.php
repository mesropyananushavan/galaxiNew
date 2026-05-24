<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\AuthorizesPolicyActions;
use App\Http\Requests\Admin\Concerns\NormalizesBooleanFormInputs;
use App\Http\Requests\Admin\Concerns\NormalizesSlugInputs;
use App\Http\Requests\Admin\Concerns\NormalizesTextFormInputs;
use App\Http\Requests\Admin\Concerns\ResolvesAdminLiveFormRedirects;
use App\Models\CardType;
use Illuminate\Foundation\Http\FormRequest;

class StoreCardTypeRequest extends FormRequest
{
    use AuthorizesPolicyActions;
    use NormalizesBooleanFormInputs;
    use NormalizesSlugInputs;
    use NormalizesTextFormInputs;
    use ResolvesAdminLiveFormRedirects;

    protected $redirectRoute = 'admin.card-types.index';

    public function authorize(): bool
    {
        return $this->authorizeCreate(CardType::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:card_types,slug'],
            'points_rate' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'review_note' => ['nullable', 'string', 'max:1000'],
            'activation_note' => ['nullable', 'string', 'max:1000'],
            'rollout_note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $status = $this->input('is_active');

        $this->merge([
            'name' => $this->normalizeTrimmedString($this->input('name')),
            'slug' => $this->normalizeSlugInput($this->input('slug')),
            'review_note' => $this->normalizeNullableTrimmedString($this->input('review_note')),
            'activation_note' => $this->normalizeNullableTrimmedString($this->input('activation_note')),
            'rollout_note' => $this->normalizeNullableTrimmedString($this->input('rollout_note')),
            'is_active' => $this->normalizeFilterBooleanInput($status),
        ]);
    }

    public function attributes(): array
    {
        return [
            'name' => 'card type name',
            'slug' => 'card type slug',
            'points_rate' => 'points rate',
            'is_active' => 'status',
            'review_note' => 'review note',
            'activation_note' => 'activation note',
            'rollout_note' => 'rollout note',
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'This card type slug is already in use.',
            'is_active.boolean' => 'The status field must be Active or Draft.',
            'review_note.max' => 'Keep the review note under 1000 characters so the tier workspace stays operator-friendly.',
            'activation_note.max' => 'Keep the activation note under 1000 characters so the tier workspace stays operator-friendly.',
            'rollout_note.max' => 'Keep the rollout note under 1000 characters so the tier workspace stays operator-friendly.',
        ];
    }

}
