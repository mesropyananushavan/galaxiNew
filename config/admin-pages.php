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
        'metrics' => [
            ['label' => 'Active tiers', 'value' => '2'],
            ['label' => 'Draft tiers', 'value' => '1'],
            ['label' => 'Imported rules', 'value' => '0'],
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
            'sections' => [
                [
                    'title' => 'Identity',
                    'help' => 'Keep tier naming close to the old Galaxy card catalog so migration mapping stays straightforward.',
                    'actions' => [
                        ['label' => 'Check duplicates', 'tone' => 'secondary'],
                    ],
                    'fields' => [
                        ['label' => 'Type name', 'value' => 'Gold'],
                        ['label' => 'Slug', 'value' => 'gold'],
                    ],
                ],
                [
                    'title' => 'Accrual settings',
                    'help' => 'These controls will later define how points and activation behavior are carried over from the old operational rules.',
                    'actions' => [
                        ['label' => 'Preview accrual', 'tone' => 'secondary'],
                    ],
                    'fields' => [
                        ['label' => 'Points rate', 'value' => '1.50'],
                        ['label' => 'Activation mode', 'value' => 'Auto after issue'],
                    ],
                ],
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
        'legacyMapping' => [
            ['label' => 'Legacy source', 'value' => 'Old Galaxy card tier catalog'],
            ['label' => 'Parity focus', 'value' => 'Tier names, accrual rules, activation behavior'],
            ['label' => 'Migration note', 'value' => 'Rebuild existing tier logic before introducing new card segmentation'],
        ],
        'activityTimeline' => [
            ['title' => 'Gold tier rules reviewed', 'time' => 'Today, 09:15', 'description' => 'Operational team confirmed that auto-activation should stay aligned with the legacy Gold workflow.'],
            ['title' => 'Partner tier held as draft', 'time' => 'Yesterday, 18:40', 'description' => 'Draft tier remains unpublished until parity checks for approval flow are complete.'],
        ],
        'readinessChecklist' => [
            ['status' => 'ready', 'label' => 'Legacy tier names mapped'],
            ['status' => 'ready', 'label' => 'Preview actions and grouped fields defined'],
            ['status' => 'pending', 'label' => 'Laravel save handler still unavailable without PHP runtime'],
        ],
        'dependencyStatus' => [
            ['label' => 'Domain model', 'value' => 'CardType model and migration skeleton exist'],
            ['label' => 'Backend dependency', 'value' => 'Form request, controller action, and persistence wiring still pending'],
            ['label' => 'Operational dependency', 'value' => 'Legacy accrual rules need live verification before publish flow is enabled'],
        ],
    ],
    'services-rules' => [
        'pageTitle' => 'Services & Rules',
        'eyebrow' => 'Catalog / Services & Rules',
        'summary' => 'Baseline management screen for service groups, eligibility rules, and business conditions that drive loyalty behavior.',
        'nextStep' => 'Replace sample controls with real rule CRUD, priority ordering, and condition editing.',
        'actions' => [
            ['label' => 'New rule', 'tone' => 'primary'],
            ['label' => 'Review priorities', 'tone' => 'secondary'],
        ],
        'metrics' => [
            ['label' => 'Active rules', 'value' => '2'],
            ['label' => 'Draft rules', 'value' => '1'],
            ['label' => 'Shop scopes', 'value' => '3'],
        ],
        'table' => [
            'columns' => ['Rule group', 'Scope', 'Condition', 'Effect', 'Priority', 'Status'],
            'rows' => [
                ['Birthday bonus', 'All shops', 'Holder birthday window', '+10% points', '10', 'active'],
                ['Partner card uplift', 'Central Shop', 'Card type = Partner', '+5% points', '20', 'active'],
                ['Night service block', 'North Shop', 'Service group = Bar', 'No accrual', '30', 'draft'],
            ],
            'filters' => ['Shop scope', 'Status', 'Rule type'],
        ],
        'form' => [
            'title' => 'Create or edit service rule',
            'sections' => [
                [
                    'title' => 'Rule identity',
                    'help' => 'Keep the rule group structure close to the old Galaxy service logic so parity remains traceable.',
                    'actions' => [
                        ['label' => 'Compare legacy rules', 'tone' => 'secondary'],
                    ],
                    'fields' => [
                        ['label' => 'Rule group', 'value' => 'Birthday bonus'],
                        ['label' => 'Scope', 'value' => 'All shops'],
                    ],
                ],
                [
                    'title' => 'Effect and priority',
                    'help' => 'Priority and effect controls will later define how overlapping loyalty conditions are resolved.',
                    'actions' => [
                        ['label' => 'Preview priority', 'tone' => 'secondary'],
                    ],
                    'fields' => [
                        ['label' => 'Effect', 'value' => '+10% points'],
                        ['label' => 'Priority', 'value' => '10'],
                    ],
                ],
            ],
            'actions' => [
                ['label' => 'Save draft', 'tone' => 'secondary'],
                ['label' => 'Publish rule', 'tone' => 'primary'],
            ],
        ],
        'emptyState' => [
            'title' => 'No service rules configured yet',
            'description' => 'Start by recreating the highest-impact legacy rule groups, then expand the rule catalog once parity is stable.',
            'actions' => [
                ['label' => 'Create first rule', 'tone' => 'primary'],
            ],
        ],
        'notice' => [
            'title' => 'Rule editing is still preview-only',
            'description' => 'This screen outlines the target Galaxy rule workflow, but save and publish actions are not wired to Laravel handlers yet.',
        ],
        'legacyMapping' => [
            ['label' => 'Legacy source', 'value' => 'Old Galaxy services and business rules'],
            ['label' => 'Parity focus', 'value' => 'Rule grouping, priority order, loyalty effect logic'],
            ['label' => 'Migration note', 'value' => 'Preserve current rule resolution behavior before extending condition syntax'],
        ],
        'activityTimeline' => [
            ['title' => 'Birthday bonus rule validated', 'time' => 'Today, 10:05', 'description' => 'Legacy rule scope was confirmed for all shops before rebuilding condition editing.'],
            ['title' => 'Night service block left in draft', 'time' => 'Yesterday, 16:20', 'description' => 'Operational team wants parity checks around bar-service exclusions before publishing.'],
        ],
        'readinessChecklist' => [
            ['status' => 'ready', 'label' => 'Legacy rule groups identified'],
            ['status' => 'ready', 'label' => 'Priority preview and parity metadata added'],
            ['status' => 'pending', 'label' => 'Rule persistence still blocked until Laravel handlers can run'],
        ],
        'dependencyStatus' => [
            ['label' => 'Domain model', 'value' => 'Service/rule domain is still preview-config only'],
            ['label' => 'Backend dependency', 'value' => 'Rule CRUD endpoints and validation are still pending'],
            ['label' => 'Operational dependency', 'value' => 'Legacy priority resolution needs verification before condition editing goes live'],
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
        'metrics' => [
            ['label' => 'Active gifts', 'value' => '2'],
            ['label' => 'Paused gifts', 'value' => '1'],
            ['label' => 'Low stock items', 'value' => '1'],
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
            'sections' => [
                [
                    'title' => 'Catalog identity',
                    'help' => 'Model the reward catalog first, then align names and point prices with the old Galaxy gift list.',
                    'actions' => [
                        ['label' => 'Compare legacy catalog', 'tone' => 'secondary'],
                    ],
                    'fields' => [
                        ['label' => 'Gift name', 'value' => 'Coffee voucher'],
                        ['label' => 'Points cost', 'value' => '150'],
                    ],
                ],
                [
                    'title' => 'Availability',
                    'help' => 'Shop scope and stock policy will become the main levers for parity with the existing redemption process.',
                    'actions' => [
                        ['label' => 'Preview stock impact', 'tone' => 'secondary'],
                    ],
                    'fields' => [
                        ['label' => 'Shop scope', 'value' => 'All shops'],
                        ['label' => 'Stock policy', 'value' => 'Unlimited'],
                    ],
                ],
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
        'legacyMapping' => [
            ['label' => 'Legacy source', 'value' => 'Old Galaxy gift and reward list'],
            ['label' => 'Parity focus', 'value' => 'Reward names, point cost, stock-aware redemption'],
            ['label' => 'Migration note', 'value' => 'Preserve the existing reward catalog shape before expanding campaign logic'],
        ],
        'activityTimeline' => [
            ['title' => 'Coffee voucher stock policy checked', 'time' => 'Today, 11:10', 'description' => 'Unlimited stock remains the baseline until real warehouse sync is wired in Laravel.'],
            ['title' => 'Premium dessert set paused', 'time' => 'Yesterday, 15:45', 'description' => 'Reward stayed paused to mirror zero-stock behavior from the legacy catalog.'],
        ],
        'readinessChecklist' => [
            ['status' => 'ready', 'label' => 'Legacy reward catalog mapped'],
            ['status' => 'ready', 'label' => 'Stock and scope preview controls defined'],
            ['status' => 'pending', 'label' => 'Real redemption and stock sync need PHP-backed flows'],
        ],
        'dependencyStatus' => [
            ['label' => 'Domain model', 'value' => 'Gift domain is still represented through config-backed preview data'],
            ['label' => 'Backend dependency', 'value' => 'CRUD handlers, stock updates, and redemption persistence are still pending'],
            ['label' => 'Operational dependency', 'value' => 'Warehouse and loyalty parity checks are needed before enabling publish flow'],
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
        'metrics' => [
            ['label' => 'Active roles', 'value' => '2'],
            ['label' => 'Draft roles', 'value' => '1'],
            ['label' => 'Scoped shops', 'value' => '3'],
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
            'sections' => [
                [
                    'title' => 'Role identity',
                    'help' => 'Start with operational roles that mirror the old Galaxy staff model before introducing new access layers.',
                    'actions' => [
                        ['label' => 'Compare staff roles', 'tone' => 'secondary'],
                    ],
                    'fields' => [
                        ['label' => 'Role name', 'value' => 'Shop Manager'],
                        ['label' => 'Scope', 'value' => 'Per shop'],
                    ],
                ],
                [
                    'title' => 'Access policy',
                    'help' => 'Permission bundles and shop policy will eventually back the real authorization matrix and assignment flow.',
                    'actions' => [
                        ['label' => 'Preview matrix impact', 'tone' => 'secondary'],
                    ],
                    'fields' => [
                        ['label' => 'Permission bundle', 'value' => 'Cards, gifts, checks'],
                        ['label' => 'Shop policy', 'value' => 'Scoped to assigned shop'],
                    ],
                ],
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
        'legacyMapping' => [
            ['label' => 'Legacy source', 'value' => 'Old Galaxy staff and access matrix'],
            ['label' => 'Parity focus', 'value' => 'Shop-scoped roles, permission bundles, cashier/manager split'],
            ['label' => 'Migration note', 'value' => 'Mirror legacy access boundaries first, then refine authorization internals'],
        ],
        'activityTimeline' => [
            ['title' => 'Shop Manager bundle reviewed', 'time' => 'Today, 08:50', 'description' => 'Cards, gifts, and checks remained grouped to preserve the legacy manager workflow.'],
            ['title' => 'Cashier draft held back', 'time' => 'Yesterday, 17:30', 'description' => 'Cashier permissions stay in draft until shop-scoped assignment rules are wired.'],
        ],
        'readinessChecklist' => [
            ['status' => 'ready', 'label' => 'Legacy role boundaries mapped'],
            ['status' => 'ready', 'label' => 'Permission bundle preview and parity notes added'],
            ['status' => 'pending', 'label' => 'Assignment and persistence flows still need PHP-backed authorization work'],
        ],
        'dependencyStatus' => [
            ['label' => 'Domain model', 'value' => 'Role and Permission models plus migration skeletons exist'],
            ['label' => 'Backend dependency', 'value' => 'Assignment UI, policy wiring, and persistence handlers are still pending'],
            ['label' => 'Operational dependency', 'value' => 'Shop-scoped access rules must be verified against legacy staff behavior before activation'],
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
