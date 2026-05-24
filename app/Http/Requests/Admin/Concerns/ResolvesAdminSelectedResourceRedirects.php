<?php

namespace App\Http\Requests\Admin\Concerns;

use Illuminate\Routing\UrlGenerator;

trait ResolvesAdminSelectedResourceRedirects
{
    protected function getRedirectUrl(): string
    {
        /** @var UrlGenerator $url */
        $url = $this->redirector->getUrlGenerator();
        $routeParameter = static::SELECTED_RESOURCE_ROUTE_PARAMETER;
        $resource = $this->route($routeParameter);

        if ($resource !== null) {
            return $url->route(
                static::SELECTED_RESOURCE_ROUTE_NAME,
                [$routeParameter => $resource],
                absolute: false,
            ).'#live-form';
        }

        return parent::getRedirectUrl();
    }
}
