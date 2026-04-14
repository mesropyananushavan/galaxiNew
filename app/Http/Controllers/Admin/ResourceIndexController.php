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
        ]);
    }
}
