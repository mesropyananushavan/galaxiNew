<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class ResourceIndexController extends Controller
{
    public function __invoke(string $resource): View
    {
        $pages = config('admin-pages');
        $defaults = config('admin-resource-page-defaults', []);

        abort_unless(is_array($pages) && array_key_exists($resource, $pages), 404);

        return view('admin.resource-index', $pages[$resource] + [
            'resourceKey' => $resource,
            'resourceBlocks' => is_array($defaults['resourceBlocks'] ?? null) ? $defaults['resourceBlocks'] : [],
            'phase' => $defaults['phase'] ?? 1,
            'pageRationale' => is_array($defaults['pageRationale'] ?? null) ? $defaults['pageRationale'] : [],
        ]);
    }
}
