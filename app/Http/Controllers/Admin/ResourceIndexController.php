<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class ResourceIndexController extends Controller
{
    public function __invoke(string $resource): View
    {
        $pages = config('admin-pages');

        // Shared shell defaults stay config-driven so the layered resource-page
        // composition can evolve without growing controller conditionals.
        $defaults = $this->defaults(config('admin-resource-page-defaults', []));

        abort_unless(is_array($pages) && array_key_exists($resource, $pages), 404);

        return view('admin.resource-index', $pages[$resource] + [
            'resourceKey' => $resource,
            'resourceBlocks' => $this->resourceBlocks($defaults),
            'phase' => $this->phase($defaults),
            'pageRationale' => $this->pageRationale($defaults),
        ]);
    }

    private function defaults(mixed $defaults): array
    {
        return is_array($defaults) ? $defaults : [];
    }

    private function resourceBlocks(array $defaults): array
    {
        return is_array($defaults['resourceBlocks'] ?? null)
            ? $defaults['resourceBlocks']
            : [];
    }

    private function pageRationale(array $defaults): array
    {
        return is_array($defaults['pageRationale'] ?? null)
            ? $defaults['pageRationale']
            : [];
    }

    private function phase(array $defaults): int
    {
        return is_int($defaults['phase'] ?? null)
            ? $defaults['phase']
            : 1;
    }
}
