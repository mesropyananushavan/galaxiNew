<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class ResourceIndexController extends Controller
{
    public function __invoke(string $resource): View
    {
        $pages = [
            'shops' => [
                'pageTitle' => 'Shops',
                'eyebrow' => 'Administration / Shops',
                'summary' => 'Placeholder index for shop scope boundaries, activation state, and future access rules.',
                'nextStep' => 'Add shop CRUD and scoped filters.',
            ],
            'cardholders' => [
                'pageTitle' => 'Cardholders',
                'eyebrow' => 'Operations / Cardholders',
                'summary' => 'Placeholder index for workers, clients, holder history, and future lifecycle actions.',
                'nextStep' => 'Add searchable cardholder table and profile actions.',
            ],
            'cards' => [
                'pageTitle' => 'Cards',
                'eyebrow' => 'Operations / Cards',
                'summary' => 'Placeholder index for card inventory, assignments, statuses, and activation tracking.',
                'nextStep' => 'Add card list, filters, and activation workflow entry points.',
            ],
            'card-types' => [
                'pageTitle' => 'Card Types',
                'eyebrow' => 'Catalog / Card Types',
                'summary' => 'Placeholder index for Galaxy card tiers, points rules, and activation settings.',
                'nextStep' => 'Add card type CRUD and business rule editing.',
            ],
            'roles-permissions' => [
                'pageTitle' => 'Roles & Permissions',
                'eyebrow' => 'Administration / Roles & Permissions',
                'summary' => 'Placeholder index for admin roles, permission bundles, and future shop-scoped access rules.',
                'nextStep' => 'Add role matrix, permission assignment, and shop-aware policy controls.',
            ],
        ];

        abort_unless(array_key_exists($resource, $pages), 404);

        return view('admin.resource-index', $pages[$resource] + [
            'resourceKey' => $resource,
        ]);
    }
}
