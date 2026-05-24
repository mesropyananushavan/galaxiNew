<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\AuthorizesPolicyActions;
use App\Models\CardType;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Validation\Rule;

class UpdateCardTypeRequest extends StoreCardTypeRequest
{
    use AuthorizesPolicyActions;

    public function authorize(): bool
    {
        return $this->authorizeUpdate('cardType', CardType::class);
    }

    public function rules(): array
    {
        $cardType = $this->route('cardType');

        return array_merge(parent::rules(), [
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('card_types', 'slug')->ignore($cardType),
            ],
        ]);
    }

    protected function getRedirectUrl(): string
    {
        /** @var UrlGenerator $url */
        $url = $this->redirector->getUrlGenerator();
        $cardType = $this->route('cardType');

        if ($cardType !== null) {
            return $url->route('admin.card-types.index', ['cardType' => $cardType], absolute: false).'#live-form';
        }

        return parent::getRedirectUrl();
    }
}
