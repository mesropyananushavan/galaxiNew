<?php

namespace App\Http\Requests\Admin\Concerns;

use Illuminate\Routing\UrlGenerator;

trait ResolvesAdminLiveFormRedirects
{
    protected function getRedirectUrl(): string
    {
        /** @var UrlGenerator $url */
        $url = $this->redirector->getUrlGenerator();

        if ($this->redirect) {
            return $url->to($this->redirect).'#live-form';
        }

        if ($this->redirectRoute) {
            return $url->route($this->redirectRoute).'#live-form';
        }

        if ($this->redirectAction) {
            return $url->action($this->redirectAction).'#live-form';
        }

        return $url->previous().'#live-form';
    }
}
