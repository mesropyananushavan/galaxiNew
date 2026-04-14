<?php

return [
    'shops' => [
        'pageTitle' => 'Shops',
        'eyebrow' => 'Administration / Shops',
        'summary' => 'Baseline operational index for shop scope boundaries, activation state, and future access rules.',
        'nextStep' => 'Replace sample rows with real shop records, manager info, and scoped access actions.',
        'table' => [
            'columns' => ['Shop', 'Code', 'Manager', 'Cardholders', 'Cards', 'Status'],
            'rows' => [
                ['Central Shop', 'central', 'Nare Gevorgyan', '248', '912', 'active'],
                ['North Shop', 'north', 'Arman Stepanyan', '121', '403', 'active'],
                ['Airport Kiosk', 'airport', 'Unassigned', '37', '84', 'paused'],
            ],
            'filters' => ['Status', 'Manager assigned', 'Volume tier'],
        ],
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
    'checks-points' => [
        'pageTitle' => 'Checks & Points',
        'eyebrow' => 'Operations / Checks & Points',
        'summary' => 'Operational placeholder for purchases, accrual events, fiscal search, and point adjustments.',
        'nextStep' => 'Add fiscal lookup, accrual history, and shop/date filters.',
        'table' => [
            'columns' => ['Receipt', 'Card', 'Shop', 'Amount', 'Points', 'Created'],
            'rows' => [
                ['CHK-90421', 'GX-100001', 'Central Shop', '24,500', '+245', '2026-04-13 18:42'],
                ['CHK-90407', 'GX-100003', 'Central Shop', '11,000', '0', '2026-04-13 14:05'],
                ['CHK-90388', 'GX-100002', 'North Shop', '7,300', '+73', '2026-04-13 10:11'],
            ],
            'filters' => ['Shop', 'Date range', 'Card number', 'Fiscal receipt'],
        ],
    ],
    'card-types' => [
        'pageTitle' => 'Card Types',
        'eyebrow' => 'Catalog / Card Types',
        'summary' => 'Baseline management screen for Galaxy card tiers, points rules, and activation settings.',
        'nextStep' => 'Replace sample controls with real CRUD handlers and validation.',
        'actions' => [
            ['label' => 'New type', 'tone' => 'primary'],
            ['label' => 'Import rules', 'tone' => 'secondary'],
        ],
        'table' => [
            'columns' => ['Type', 'Slug', 'Points rate', 'Activation', 'Status'],
            'rows' => [
                ['Gold', 'gold', '1.50x', 'Auto after issue', 'active'],
                ['Silver', 'silver', '1.00x', 'Manual', 'active'],
                ['Partner', 'partner', '1.20x', 'Manager approval', 'draft'],
            ],
            'filters' => ['Status', 'Activation mode', 'Points rate'],
        ],
        'form' => [
            'title' => 'Create or edit card type',
            'fields' => [
                ['label' => 'Type name', 'value' => 'Gold'],
                ['label' => 'Slug', 'value' => 'gold'],
                ['label' => 'Points rate', 'value' => '1.50'],
                ['label' => 'Activation mode', 'value' => 'Auto after issue'],
            ],
            'actions' => [
                ['label' => 'Save draft', 'tone' => 'secondary'],
                ['label' => 'Publish type', 'tone' => 'primary'],
            ],
        ],
        'emptyState' => [
            'title' => 'No custom card types configured yet',
            'description' => 'Start by creating the first Galaxy-specific card tier, then import or rebuild rules from the old operational setup.',
            'actions' => [
                ['label' => 'Create first type', 'tone' => 'primary'],
            ],
        ],
        'notice' => [
            'title' => 'Card type rules are still preview-only',
            'description' => 'This screen is shaping the final Galaxy management flow, but save and publish actions are not connected to Laravel handlers yet.',
        ],
    ],
    'services-rules' => [
        'pageTitle' => 'Services & Rules',
        'eyebrow' => 'Catalog / Services & Rules',
        'summary' => 'Operational placeholder for service groups, eligibility rules, and business conditions that affect loyalty flows.',
        'nextStep' => 'Add service group CRUD, rule priority, and condition editing.',
        'table' => [
            'columns' => ['Rule group', 'Scope', 'Condition', 'Effect', 'Priority', 'Status'],
            'rows' => [
                ['Birthday bonus', 'All shops', 'Holder birthday window', '+10% points', '10', 'active'],
                ['Partner card uplift', 'Central Shop', 'Card type = Partner', '+5% points', '20', 'active'],
                ['Night service block', 'North Shop', 'Service group = Bar', 'No accrual', '30', 'draft'],
            ],
            'filters' => ['Shop scope', 'Status', 'Rule type'],
        ],
    ],
    'gifts' => [
        'pageTitle' => 'Gifts',
        'eyebrow' => 'Catalog / Gifts',
        'summary' => 'Baseline management screen for gift catalog, redemption settings, and stock-aware reward management.',
        'nextStep' => 'Replace sample controls with real gift CRUD, stock tracking, and redemption flows.',
        'actions' => [
            ['label' => 'New gift', 'tone' => 'primary'],
            ['label' => 'Stock audit', 'tone' => 'secondary'],
        ],
        'table' => [
            'columns' => ['Gift', 'Points cost', 'Shop scope', 'Stock', 'Status'],
            'rows' => [
                ['Coffee voucher', '150', 'All shops', 'Unlimited', 'active'],
                ['Airport transfer', '900', 'Airport Kiosk', '12', 'active'],
                ['Premium dessert set', '450', 'Central Shop', '0', 'paused'],
            ],
            'filters' => ['Shop scope', 'Availability', 'Points range'],
        ],
        'form' => [
            'title' => 'Create or edit gift',
            'fields' => [
                ['label' => 'Gift name', 'value' => 'Coffee voucher'],
                ['label' => 'Points cost', 'value' => '150'],
                ['label' => 'Shop scope', 'value' => 'All shops'],
                ['label' => 'Stock policy', 'value' => 'Unlimited'],
            ],
            'actions' => [
                ['label' => 'Save draft', 'tone' => 'secondary'],
                ['label' => 'Publish gift', 'tone' => 'primary'],
            ],
        ],
        'emptyState' => [
            'title' => 'No gift campaigns configured yet',
            'description' => 'Use the management flow to add the first redeemable reward, then align stock and shop scope with the old Galaxy catalog.',
            'actions' => [
                ['label' => 'Create first gift', 'tone' => 'primary'],
            ],
        ],
        'notice' => [
            'title' => 'Gift redemption controls are still preview-only',
            'description' => 'This shell defines the target Galaxy workflow, but inventory and publishing actions are not wired to backend requests yet.',
        ],
    ],
    'roles-permissions' => [
        'pageTitle' => 'Roles & Permissions',
        'eyebrow' => 'Administration / Roles & Permissions',
        'summary' => 'Baseline management screen for admin roles, permission bundles, and future shop-scoped access rules.',
        'nextStep' => 'Replace sample controls with real role assignment, permission matrix, and shop-aware policy flows.',
        'actions' => [
            ['label' => 'New role', 'tone' => 'primary'],
            ['label' => 'Review matrix', 'tone' => 'secondary'],
        ],
        'table' => [
            'columns' => ['Role', 'Scope', 'Key permissions', 'Users', 'Status'],
            'rows' => [
                ['Super Admin', 'All shops', 'Full access', '2', 'active'],
                ['Shop Manager', 'Per shop', 'Cards, gifts, checks', '8', 'active'],
                ['Cashier', 'Per shop', 'Checks, card lookup', '14', 'draft'],
            ],
            'filters' => ['Scope', 'Status', 'Permission set'],
        ],
        'form' => [
            'title' => 'Create or edit role',
            'fields' => [
                ['label' => 'Role name', 'value' => 'Shop Manager'],
                ['label' => 'Scope', 'value' => 'Per shop'],
                ['label' => 'Permission bundle', 'value' => 'Cards, gifts, checks'],
                ['label' => 'Shop policy', 'value' => 'Scoped to assigned shop'],
            ],
            'actions' => [
                ['label' => 'Save draft', 'tone' => 'secondary'],
                ['label' => 'Publish role', 'tone' => 'primary'],
            ],
        ],
        'emptyState' => [
            'title' => 'No shop-scoped roles configured yet',
            'description' => 'Create the first operational role set so shop managers and cashiers can map cleanly to the old Galaxy access model.',
            'actions' => [
                ['label' => 'Create first role', 'tone' => 'primary'],
            ],
        ],
        'notice' => [
            'title' => 'Role publishing is still preview-only',
            'description' => 'The access matrix and role editor are visible now, but permission persistence and assignment flows still need Laravel-side implementation.',
        ],
    ],
    'reports' => [
        'pageTitle' => 'Reports',
        'eyebrow' => 'Administration / Reports',
        'summary' => 'Operational placeholder for analytics, histories, and export-oriented admin reporting.',
        'nextStep' => 'Add report catalog, date-range presets, and export entry points.',
        'table' => [
            'columns' => ['Report', 'Scope', 'Default period', 'Format', 'Status'],
            'rows' => [
                ['Points accrual summary', 'All shops', 'Last 30 days', 'XLSX', 'planned'],
                ['Card activity history', 'Per shop', 'Last 7 days', 'Table', 'planned'],
                ['Gift redemption report', 'All shops', 'Month to date', 'CSV', 'planned'],
            ],
            'filters' => ['Shop scope', 'Period preset', 'Report type'],
        ],
    ],
];
