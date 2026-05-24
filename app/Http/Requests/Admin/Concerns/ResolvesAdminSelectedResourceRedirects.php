<?php

namespace App\Http\Requests\Admin\Concerns;

use Illuminate\Routing\UrlGenerator;

trait ResolvesAdminSelectedResourceRedirects
{
    protected function selectedResourceRouteParameter(): string
    {
        return static::SELECTED_RESOURCE_ROUTE_PARAMETER;
    }

    protected function selectedResource(): mixed
    {
        return $this->route($this->selectedResourceRouteParameter());
    }

    protected function getRedirectUrl(): string
    {
        /** @var UrlGenerator $url */
        $url = $this->redirector->getUrlGenerator();
        $routeParameter = $this->selectedResourceRouteParameter();
        $resource = $this->selectedResource();

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
