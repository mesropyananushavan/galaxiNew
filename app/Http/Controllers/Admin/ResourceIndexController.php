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
                'summary' => 'Baseline operational index for workers, clients, holder history, and future lifecycle actions.',
                'nextStep' => 'Replace sample rows with real holder search, profile links, and status actions.',
                'table' => [
                    'columns' => ['Name', 'Phone', 'Shop', 'Cards', 'Status', 'Last activity'],
                    'rows' => [
                        ['Anna Petrova', '+374 91 100001', 'Central Shop', '2', 'active', '2026-04-13'],
                        ['Mariam Sargsyan', '+374 91 100002', 'Central Shop', '1', 'active', '2026-04-12'],
                        ['Arman Hakobyan', '+374 91 100003', 'North Shop', '0', 'inactive', '2026-03-30'],
                    ],
                    'filters' => ['Shop', 'Status', 'Has cards', 'Activity period'],
                ],
            ],
            'cards' => [
                'pageTitle' => 'Cards',
                'eyebrow' => 'Operations / Cards',
                'summary' => 'Baseline operational index for card inventory, assignments, statuses, and activation tracking.',
                'nextStep' => 'Replace sample rows with real query-backed inventory and status filters.',
                'table' => [
                    'columns' => ['Number', 'Holder', 'Type', 'Shop', 'Status', 'Activated'],
                    'rows' => [
                        ['GX-100001', 'Anna Petrova', 'Gold', 'Central Shop', 'active', '2026-04-10'],
                        ['GX-100002', 'Unassigned', 'Silver', 'North Shop', 'draft', '—'],
                        ['GX-100003', 'Mariam Sargsyan', 'Partner', 'Central Shop', 'blocked', '2026-03-28'],
                    ],
                    'filters' => ['Shop', 'Status', 'Card type', 'Activation period'],
                ],
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
