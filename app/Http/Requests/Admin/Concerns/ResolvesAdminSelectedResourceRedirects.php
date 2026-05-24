<?php

namespace App\Http\Requests\Admin\Concerns;

use Illuminate\Routing\UrlGenerator;

trait ResolvesAdminSelectedResourceRedirects
{
    protected function redirectToSelectedResource(string $routeParameter, string $routeName): string
    {
        /** @var UrlGenerator $url */
        $url = $this->redirector->getUrlGenerator();
        $resource = $this->route($routeParameter);

        if ($resource !== null) {
            return $url->route($routeName, [$routeParameter => $resource], absolute: false).'#live-form';
        }

        return parent::getRedirectUrl();
    }
}
