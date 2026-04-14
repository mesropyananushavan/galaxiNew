<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class ResourceIndexController extends Controller
{
    public function __invoke(string $resource): View
    {
        $pages = config('admin-pages');
        $resourceBlocks = config('admin-resource-blocks', []);

        abort_unless(is_array($pages) && array_key_exists($resource, $pages), 404);

        return view('admin.resource-index', $pages[$resource] + [
            'resourceKey' => $resource,
            'resourceBlocks' => is_array($resourceBlocks) ? $resourceBlocks : [],
            'pageRationale' => [
                'connect the admin navigation to real Galaxy sections instead of dead placeholders;',
                'reserve stable route names for future CRUD and reporting flows;',
                'make the Phase 1 shell visibly closer to the old operational product shape.',
            ],
        ]);
    }
}
