<?php

namespace App\Http\Requests\Admin\Concerns;

trait AuthorizesPolicyActions
{
    protected function authorizeCreate(string $modelClass): bool
    {
        return $this->user()?->can('create', $modelClass) ?? false;
    }

    protected function authorizeSelectedResourceUpdate(string $modelClass): bool
    {
        return $this->authorizeUpdate(static::SELECTED_RESOURCE_ROUTE_PARAMETER, $modelClass);
    }

    protected function authorizeUpdate(string $routeParameter, string $modelClass): bool
    {
        $model = $this->route($routeParameter);

        return $model instanceof $modelClass && ($this->user()?->can('update', $model) ?? false);
    }
}
