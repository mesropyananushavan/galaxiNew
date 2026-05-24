<?php

namespace App\Http\Requests\Admin\Concerns;

use Illuminate\Routing\UrlGenerator;

trait ResolvesAdminSelectedResourceRedirects
{
    abstract protected function selectedResourceRouteParameter(): string;

    abstract protected function selectedResourceRouteName(): string;

    protected function getRedirectUrl(): string
    {
        /** @var UrlGenerator $url */
        $url = $this->redirector->getUrlGenerator();
        $routeParameter = $this->selectedResourceRouteParameter();
        $resource = $this->route($routeParameter);

        if ($resource !== null) {
            return $url->route(
                $this->selectedResourceRouteName(),
                [$routeParameter => $resource],
                absolute: false,
            ).'#live-form';
        }

        return parent::getRedirectUrl();
    }
}
