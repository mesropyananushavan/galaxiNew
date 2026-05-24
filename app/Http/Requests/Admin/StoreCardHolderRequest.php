<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\AuthorizesPolicyActions;
use App\Http\Requests\Admin\Concerns\NormalizesBooleanFormInputs;
use App\Http\Requests\Admin\Concerns\NormalizesTextFormInputs;
use App\Http\Requests\Admin\Concerns\ResolvesAdminLiveFormRedirects;
use App\Http\Requests\Admin\Concerns\ValidatesAccessibleShop;
use App\Models\CardHolder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCardHolderRequest extends FormRequest
{
    use AuthorizesPolicyActions;
    use NormalizesBooleanFormInputs;
    use NormalizesTextFormInputs;
    use ResolvesAdminLiveFormRedirects;
    use ValidatesAccessibleShop;

    protected $redirectRoute = 'admin.cardholders.index';

    public function authorize(): bool
    {
        return $this->authorizeCreate(CardHolder::class);
    }

    public function rules(): array
    {
        return [
            'shop_id' => ['required', 'integer', 'exists:shops,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'review_note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $status = $this->input('is_active');

        $this->merge([
            'full_name' => $this->normalizeTrimmedString($this->input('full_name')),
            'phone' => $this->normalizeNullableTrimmedString($this->input('phone')),
            'email' => ($normalizedEmail = $this->normalizeNullableTrimmedString($this->input('email'))) !== null && is_string($normalizedEmail)
                ? strtolower($normalizedEmail)
                : $normalizedEmail,
            'review_note' => $this->normalizeNullableTrimmedString($this->input('review_note')),
            'is_active' => $this->normalizeBooleanInput($status),
        ]);
    }

    public function attributes(): array
    {
        return [
            'shop_id' => 'shop',
            'full_name' => 'cardholder name',
            'phone' => 'phone number',
            'email' => 'email address',
            'is_active' => 'cardholder status',
            'review_note' => 'review note',
        ];
    }

    public function messages(): array
    {
        return [
            'review_note.max' => 'Keep the review note under 1000 characters so the holder workspace stays operator-friendly.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $this->validateAccessibleShop(
            $validator,
            'Choose a shop you can access so the Galaxy holder shell stays scoped to your assigned branch.',
        );
    }

}
