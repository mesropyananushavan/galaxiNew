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
                'summary' => 'Placeholder index for Galaxy card tiers, points rules, and activation settings.',
                'nextStep' => 'Add card type CRUD and business rule editing.',
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
                'summary' => 'Operational placeholder for gift catalog, redemption settings, and stock-aware reward management.',
                'nextStep' => 'Add gift CRUD, stock tracking, and redemption controls.',
                'table' => [
                    'columns' => ['Gift', 'Points cost', 'Shop scope', 'Stock', 'Status'],
                    'rows' => [
                        ['Coffee voucher', '150', 'All shops', 'Unlimited', 'active'],
                        ['Airport transfer', '900', 'Airport Kiosk', '12', 'active'],
                        ['Premium dessert set', '450', 'Central Shop', '0', 'paused'],
                    ],
                    'filters' => ['Shop scope', 'Availability', 'Points range'],
                ],
            ],
            'roles-permissions' => [
                'pageTitle' => 'Roles & Permissions',
                'eyebrow' => 'Administration / Roles & Permissions',
                'summary' => 'Placeholder index for admin roles, permission bundles, and future shop-scoped access rules.',
                'nextStep' => 'Add role matrix, permission assignment, and shop-aware policy controls.',
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

        abort_unless(array_key_exists($resource, $pages), 404);

        return view('admin.resource-index', $pages[$resource] + [
            'resourceKey' => $resource,
        ]);
    }
}
