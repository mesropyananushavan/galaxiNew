<?php

return [
    [
        'group' => 'Operations',
        'items' => [
            [
                'label' => 'Dashboard',
                'route' => 'admin.dashboard',
                'description' => 'Operational overview and shortcuts.',
            ],
            [
                'label' => 'Cardholders',
                'route' => 'admin.cardholders.index',
                'description' => 'Workers, clients, and holder history.',
            ],
            [
                'label' => 'Cards',
                'route' => 'admin.cards.index',
                'description' => 'Card inventory, status, and assignments.',
            ],
            [
                'label' => 'Checks & Points',
                'route' => 'admin.checks-points.index',
                'description' => 'Purchases, accrual, and fiscal search.',
            ],
        ],
    ],
    [
        'group' => 'Catalog',
        'items' => [
            [
                'label' => 'Card Types',
                'route' => 'admin.card-types.index',
                'description' => 'Card tiers, limits, and rules.',
            ],
            [
                'label' => 'Services & Rules',
                'route' => 'admin.services-rules.index',
                'description' => 'Service groups, conditions, and business rules.',
            ],
            [
                'label' => 'Gifts',
                'route' => 'admin.gifts.index',
                'description' => 'Gift catalog and redemption settings.',
            ],
        ],
    ],
    [
        'group' => 'Administration',
        'items' => [
            [
                'label' => 'Shops',
                'route' => 'admin.shops.index',
                'description' => 'Galaxy branch catalog and scope boundaries.',
            ],
            [
                'label' => 'Roles & Permissions',
                'route' => 'admin.roles-permissions.index',
                'description' => 'Galaxy access shells, permissions, and access baseline.',
            ],
            [
                'label' => 'Reports',
                'route' => 'admin.reports.index',
                'description' => 'Galaxy reporting analytics and source histories.',
            ],
        ],
    ],
];
