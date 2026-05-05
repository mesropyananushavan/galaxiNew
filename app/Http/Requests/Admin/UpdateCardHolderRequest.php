<?php

namespace App\Http\Requests\Admin;

use Illuminate\Routing\UrlGenerator;

class UpdateCardHolderRequest extends StoreCardHolderRequest
{
    protected function getRedirectUrl(): string
    {
        /** @var UrlGenerator $url */
        $url = $this->redirector->getUrlGenerator();
        $cardholder = $this->route('cardholder');

        if ($cardholder !== null) {
            return $url->route('admin.cardholders.index', ['cardholder' => $cardholder], absolute: false).'#live-form';
        }

        return parent::getRedirectUrl();
    }
}
