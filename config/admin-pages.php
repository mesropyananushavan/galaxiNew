<?php

return [
    'shops' => [
        'pageTitle' => 'Shops',
        'eyebrow' => 'Administration / Shops',
        'summary' => 'Baseline operational index for shop scope boundaries, activation state, and future access rules.',
        'nextStep' => 'Replace sample rows with real shop records, manager info, and scoped access actions.',
        'actions' => [
            [
                'label' => 'New shop',
                'tone' => 'primary',
                'disabled' => true,
                'disabledReason' => 'Blocked until the first Laravel-backed shops index and manager assignment parity checks are verified.',
            ],
            [
                'label' => 'Review branch scope',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => 'Blocked until branch ownership rules are confirmed against the legacy Galaxy multi-shop access model.',
            ],
        ],
        'metrics' => [
            ['label' => 'Active shops', 'value' => '2'],
            ['label' => 'Paused shops', 'value' => '1'],
            ['label' => 'Assigned managers', 'value' => '2'],
        ],
        'table' => [
            'columns' => ['Shop', 'Code', 'Manager', 'Cardholders', 'Cards', 'Status'],
            'rows' => [
                ['Central Shop', 'central', 'Nare Gevorgyan', '248', '912', 'active'],
                ['North Shop', 'north', 'Arman Stepanyan', '121', '403', 'active'],
                ['Airport Kiosk', 'airport', 'Unassigned', '37', '84', 'paused'],
            ],
            'filters' => ['Status', 'Manager assigned', 'Volume tier'],
        ],
        'operationalGlossary' => [
            ['term' => 'Shop scope', 'meaning' => 'The visibility boundary that later controls who can manage cards and reports per location.'],
            ['term' => 'Manager assigned', 'meaning' => 'The legacy operator responsible for the branch in the old Galaxy workflow.'],
        ],
        'legacyParityNotes' => [
            'Preserve branch-level visibility before introducing broader admin views.',
            'Keep manager assignment semantics aligned with the old Galaxy branch ownership model.',
        ],
        'operationalNextSlice' => [
            'summary' => 'When backend execution is available, start by replacing static shop rows with a minimal query-backed index.',
            'steps' => [
                'Load shops with manager and status columns from Eloquent.',
                'Keep filters shallow at first, status and assigned manager only.',
                'Add row actions only after the read path is stable.',
            ],
        ],
        'operationalDataStatus' => [
            ['label' => 'Current source', 'value' => 'Static preview rows from config/admin-pages.php'],
            ['label' => 'Target source', 'value' => 'Shop records and manager relations from Eloquent'],
            ['label' => 'Blocker', 'value' => 'PHP runtime is unavailable, so real query wiring cannot be validated yet'],
        ],
        'operationalMigrationBlockers' => [
            'PHP runtime is missing, so the first real shop query path cannot be executed locally.',
            'Manager ownership rules still need parity verification against the old Galaxy branch model.',
        ],
        'notice' => [
            'title' => 'Shop operations are still preview-only',
            'description' => 'Branch actions, metrics, and filters are shaping the final Galaxy workspace, but they are not wired to Laravel queries or handlers yet.',
        ],
        'readinessChecklist' => [
            ['status' => 'ready', 'label' => 'Preview shop rows and branch actions defined'],
            ['status' => 'ready', 'label' => 'Operational parity cues for scope and manager ownership are visible'],
            ['status' => 'pending', 'label' => 'Real shop queries and branch mutations still need PHP-backed Laravel wiring'],
        ],
        'dependencyStatus' => [
            ['label' => 'Domain model', 'value' => 'Shop model and user-to-shop linkage baseline exist'],
            ['label' => 'Backend dependency', 'value' => 'Query-backed shop index and branch actions are still pending'],
            ['label' => 'Operational dependency', 'value' => 'Legacy branch ownership and manager assignment rules still need live verification'],
        ],
        'implementationHandoff' => [
            'summary' => 'When PHP is available, start with a minimal read-only shop index before adding any branch mutation flows.',
            'steps' => [
                'Wire an Eloquent-backed shops index with manager and status columns.',
                'Keep the first filter set to status and assigned manager only.',
                'Delay create and edit actions until the read path is stable against live data.',
            ],
        ],
        'activityTimeline' => [
            ['title' => 'Central Shop scope reviewed', 'time' => 'Today, 17:40', 'description' => 'Branch ownership and manager visibility were checked against the old Galaxy operating model.'],
            ['title' => 'Airport Kiosk kept paused', 'time' => 'Yesterday, 15:10', 'description' => 'The preview state preserves a paused branch case for parity before real status flows exist.'],
        ],
        'legacyMapping' => [
            ['label' => 'Legacy source', 'value' => 'Old Galaxy branch administration screen'],
            ['label' => 'Parity focus', 'value' => 'Branch scope, manager assignment, active versus paused visibility'],
            ['label' => 'Migration note', 'value' => 'Keep branch ownership semantics stable before introducing new multi-shop controls'],
        ],
        'operatorChecklist' => [
            'summary' => 'Daily branch oversight in the old Galaxy workspace was built around quick visual checks before anyone opened a detail screen.',
            'items' => [
                'Review paused branches before shift handoff.',
                'Confirm each active shop still has an assigned manager.',
                'Escalate scope mismatches before changing branch visibility rules.',
            ],
        ],
        'escalationGuide' => [
            'summary' => 'Branch issues in the legacy admin usually moved through a short escalation path instead of ad hoc edits.',
            'items' => [
                'Route manager-assignment gaps to operations supervision first.',
                'Escalate cross-shop visibility disputes before changing access scope.',
                'Treat paused-branch recovery as an approval step, not a same-screen quick fix.',
            ],
        ],
        'shiftHandoff' => [
            'summary' => 'Shop oversight in the old Galaxy console depended on explicit handoff notes so the next operator could continue branch monitoring without rechecking everything.',
            'items' => [
                'Carry paused-branch context into the next shift until recovery is approved.',
                'Flag any shop that still lacks a manager assignment at handoff time.',
                'Note unresolved scope disputes before the next operator touches branch visibility.',
            ],
        ],
        'openIssues' => [
            'summary' => 'The old Galaxy branch screen usually kept a short list of unresolved branch items mentally attached to the shift.',
            'items' => [
                'Airport Kiosk remains paused pending recovery approval.',
                'Any unassigned manager state should stay visible until ownership is confirmed.',
                'Cross-shop visibility disagreements must remain open until scope is verified.',
            ],
        ],
    ],
    'cardholders' => [
        'pageTitle' => 'Cardholders',
        'eyebrow' => 'Operations / Cardholders',
        'summary' => 'Baseline operational index for workers, clients, holder history, and future lifecycle actions.',
        'nextStep' => 'Replace sample rows with real holder search, profile links, and status actions.',
        'actions' => [
            ['label' => 'New cardholder', 'tone' => 'primary'],
            ['label' => 'Review recent activity', 'tone' => 'secondary'],
        ],
        'metrics' => [
            ['label' => 'Active holders', 'value' => '2'],
            ['label' => 'Inactive holders', 'value' => '1'],
            ['label' => 'Linked cards', 'value' => '3'],
        ],
        'table' => [
            'columns' => ['Name', 'Phone', 'Shop', 'Cards', 'Status', 'Last activity'],
            'rows' => [
                ['Anna Petrova', '+374 91 100001', 'Central Shop', '2', 'active', '2026-04-13'],
                ['Mariam Sargsyan', '+374 91 100002', 'Central Shop', '1', 'active', '2026-04-12'],
                ['Arman Hakobyan', '+374 91 100003', 'North Shop', '0', 'inactive', '2026-03-30'],
            ],
            'filters' => ['Shop', 'Status', 'Has cards', 'Activity period'],
        ],
        'operationalGlossary' => [
            ['term' => 'Cardholder', 'meaning' => 'The person record that ties workers or clients to one or more loyalty cards.'],
            ['term' => 'Last activity', 'meaning' => 'The latest card or purchase interaction that should later come from real event history.'],
        ],
        'legacyParityNotes' => [
            'Keep worker and client lookup fast, with minimal page hopping.',
            'Preserve the operational emphasis on recent activity and card linkage.',
        ],
        'operationalNextSlice' => [
            'summary' => 'The first backend slice should be a searchable cardholder index before profile editing is introduced.',
            'steps' => [
                'Query cardholders with shop and status columns.',
                'Add simple search by name or phone before advanced filters.',
                'Defer detailed activity history until the index read path is real.',
            ],
        ],
        'operationalDataStatus' => [
            ['label' => 'Current source', 'value' => 'Static preview rows from config/admin-pages.php'],
            ['label' => 'Target source', 'value' => 'CardHolder records with shop linkage and recent activity data'],
            ['label' => 'Blocker', 'value' => 'PHP runtime is unavailable, so real query wiring cannot be validated yet'],
        ],
        'operationalMigrationBlockers' => [
            'PHP runtime is missing, so searchable cardholder queries cannot be exercised locally.',
            'Recent activity still needs a stable event source before the preview can become a real index.',
        ],
        'notice' => [
            'title' => 'Cardholder operations are still preview-only',
            'description' => 'Search actions, metrics, and lifecycle cues are shaping the target Galaxy flow, but they are not backed by Laravel reads or writes yet.',
        ],
        'readinessChecklist' => [
            ['status' => 'ready', 'label' => 'Preview holder search surface and activity cues are defined'],
            ['status' => 'ready', 'label' => 'Operational parity notes for lookup speed and linkage are visible'],
            ['status' => 'pending', 'label' => 'Search, profile reads, and activity history still need PHP-backed Laravel wiring'],
        ],
        'dependencyStatus' => [
            ['label' => 'Domain model', 'value' => 'CardHolder model and shop linkage baseline exist'],
            ['label' => 'Backend dependency', 'value' => 'Searchable index, profile read path, and activity sourcing are still pending'],
            ['label' => 'Operational dependency', 'value' => 'Recent activity source and client-worker lookup parity still need validation'],
        ],
        'implementationHandoff' => [
            'summary' => 'When PHP is available, start with a searchable cardholder index before attempting profile edits or lifecycle actions.',
            'steps' => [
                'Load cardholders with shop, status, and linked-card counts.',
                'Add simple search by name or phone as the first interaction slice.',
                'Defer detailed profile reads and activity history until the index path is stable.',
            ],
        ],
        'activityTimeline' => [
            ['title' => 'Anna Petrova activity pattern reviewed', 'time' => 'Today, 16:05', 'description' => 'The preview keeps recent activity visible to match the old operator lookup flow.'],
            ['title' => 'North Shop inactive holder retained', 'time' => 'Yesterday, 13:25', 'description' => 'An inactive cardholder case stays visible for parity before real lifecycle filters are wired.'],
        ],
        'legacyMapping' => [
            ['label' => 'Legacy source', 'value' => 'Old Galaxy holder and client lookup screen'],
            ['label' => 'Parity focus', 'value' => 'Fast search, linked cards, recent activity visibility'],
            ['label' => 'Migration note', 'value' => 'Preserve operator lookup speed before expanding profile detail flows'],
        ],
        'operatorChecklist' => [
            'summary' => 'Legacy operators typically used the holder list as a fast intervention surface, not just a directory.',
            'items' => [
                'Search inactive holders before creating duplicate profiles.',
                'Check linked-card counts before escalating missing-card complaints.',
                'Use recent activity as the first triage signal before opening full history.',
            ],
        ],
        'escalationGuide' => [
            'summary' => 'Holder issues in the old workflow followed a predictable escalation pattern depending on identity, card linkage, and recent activity.',
            'items' => [
                'Escalate duplicate-profile suspicions before creating a replacement holder record.',
                'Route missing-card disputes to card operations after confirming linkage state.',
                'Treat stale activity history as a data-source problem before promising profile fixes.',
            ],
        ],
        'shiftHandoff' => [
            'summary' => 'Cardholder support in the legacy admin relied on clear handoff notes so unresolved identity and linkage issues did not restart from zero on the next shift.',
            'items' => [
                'Carry duplicate-profile investigations into the next shift until identity is confirmed.',
                'Leave missing-card cases tagged with the last verified linkage state.',
                'Note stale-activity complaints that still need data-source follow-up.',
            ],
        ],
        'openIssues' => [
            'summary' => 'Operators in the old holder lookup flow kept a compact set of unresolved people and linkage problems in view between shifts.',
            'items' => [
                'Potential duplicate-holder cases stay open until identity is confirmed.',
                'Missing-card complaints remain open until linkage and inventory paths agree.',
                'Stale activity timelines remain open until an event-source explanation exists.',
            ],
        ],
    ],
    'cards' => [
        'pageTitle' => 'Cards',
        'eyebrow' => 'Operations / Cards',
        'summary' => 'Baseline operational index for card inventory, assignments, statuses, and activation tracking.',
        'nextStep' => 'Replace sample rows with real query-backed inventory and status filters.',
        'actions' => [
            ['label' => 'Issue card', 'tone' => 'primary'],
            ['label' => 'Review blocked cards', 'tone' => 'secondary'],
        ],
        'metrics' => [
            ['label' => 'Active cards', 'value' => '1'],
            ['label' => 'Draft cards', 'value' => '1'],
            ['label' => 'Blocked cards', 'value' => '1'],
        ],
        'table' => [
            'columns' => ['Number', 'Holder', 'Type', 'Shop', 'Status', 'Activated'],
            'rows' => [
                ['GX-100001', 'Anna Petrova', 'Gold', 'Central Shop', 'active', '2026-04-10'],
                ['GX-100002', 'Unassigned', 'Silver', 'North Shop', 'draft', '—'],
                ['GX-100003', 'Mariam Sargsyan', 'Partner', 'Central Shop', 'blocked', '2026-03-28'],
            ],
            'filters' => ['Shop', 'Status', 'Card type', 'Activation period'],
        ],
        'operationalGlossary' => [
            ['term' => 'Card type', 'meaning' => 'The tier or segment definition that controls accrual and activation behavior.'],
            ['term' => 'Activated', 'meaning' => 'The timestamp when a physical or virtual card became usable in the loyalty flow.'],
        ],
        'legacyParityNotes' => [
            'Retain clear visibility for unassigned, active, and blocked card states.',
            'Keep activation timing visible without opening a separate detail screen.',
        ],
        'operationalNextSlice' => [
            'summary' => 'The first real cards slice should be a query-backed inventory table that mirrors the current preview layout.',
            'steps' => [
                'Load cards with holder, type, status, and activation timestamp fields.',
                'Keep the first filter set to shop, status, and card type.',
                'Delay mutation actions until the read path is verified against live data.',
            ],
        ],
        'operationalDataStatus' => [
            ['label' => 'Current source', 'value' => 'Static preview rows from config/admin-pages.php'],
            ['label' => 'Target source', 'value' => 'Card records joined with holders, card types, and shops'],
            ['label' => 'Blocker', 'value' => 'PHP runtime is unavailable, so real query wiring cannot be validated yet'],
        ],
        'operationalMigrationBlockers' => [
            'PHP runtime is missing, so the first real inventory query cannot be executed locally.',
            'Status semantics still need verification against legacy blocked and draft card behavior.',
        ],
        'notice' => [
            'title' => 'Card operations are still preview-only',
            'description' => 'Inventory actions, status metrics, and filters are laid out for Galaxy parity, but they are not connected to Laravel handlers yet.',
        ],
        'readinessChecklist' => [
            ['status' => 'ready', 'label' => 'Preview inventory statuses and card-type filters are defined'],
            ['status' => 'ready', 'label' => 'Operational parity cues for blocked and draft cards are visible'],
            ['status' => 'pending', 'label' => 'Inventory queries and card lifecycle handlers still need PHP-backed Laravel wiring'],
        ],
        'dependencyStatus' => [
            ['label' => 'Domain model', 'value' => 'Card and CardType models plus migration skeletons exist'],
            ['label' => 'Backend dependency', 'value' => 'Inventory reads, assignment flows, and status mutations are still pending'],
            ['label' => 'Operational dependency', 'value' => 'Legacy card-state semantics still need live verification before action wiring'],
        ],
        'implementationHandoff' => [
            'summary' => 'When PHP is available, start with a read-only inventory table before exposing issue, block, or assignment flows.',
            'steps' => [
                'Load cards with holder, type, status, and activation timestamp columns.',
                'Add shop, status, and card-type filters before any mutations.',
                'Delay issue and block actions until the read path is verified against live card states.',
            ],
        ],
        'activityTimeline' => [
            ['title' => 'Blocked card state kept visible', 'time' => 'Today, 18:20', 'description' => 'The blocked preview row remains in place to preserve old Galaxy card-state visibility.'],
            ['title' => 'Draft card review deferred', 'time' => 'Yesterday, 12:10', 'description' => 'Draft issuance behavior stays preview-only until real inventory reads can be verified.'],
        ],
        'legacyMapping' => [
            ['label' => 'Legacy source', 'value' => 'Old Galaxy card inventory screen'],
            ['label' => 'Parity focus', 'value' => 'Card states, holder linkage, activation visibility'],
            ['label' => 'Migration note', 'value' => 'Preserve blocked and draft semantics before exposing mutation actions'],
        ],
        'operatorChecklist' => [
            'summary' => 'Card inventory work in the legacy system centered on status triage before any mutation workflow was attempted.',
            'items' => [
                'Review blocked cards before issuing new replacements.',
                'Check draft inventory before escalating card shortages.',
                'Confirm holder linkage before treating a card as unassigned stock.',
            ],
        ],
        'escalationGuide' => [
            'summary' => 'Legacy card operations used a narrow escalation path so card-state problems were not mistaken for simple inventory actions.',
            'items' => [
                'Escalate blocked-card disputes before issuing a fresh card number.',
                'Route draft-stock shortages to branch inventory review before manual overrides.',
                'Treat holder-link mismatches as identity issues before stock corrections.',
            ],
        ],
        'shiftHandoff' => [
            'summary' => 'Card inventory continuity in the old Galaxy workspace depended on precise handoff notes around blocked stock, draft stock, and unresolved holder linkage.',
            'items' => [
                'Carry blocked-card disputes into the next shift until replacement is approved.',
                'Flag draft-stock shortages that still need branch review.',
                'Leave holder-link mismatches documented before another operator issues stock changes.',
            ],
        ],
        'openIssues' => [
            'summary' => 'The old card inventory screen usually held a short queue of unresolved state problems that operators revisited across shifts.',
            'items' => [
                'Blocked-card disputes remain open until replacement approval is explicit.',
                'Draft-stock shortages remain open until branch inventory review is complete.',
                'Holder-link mismatches remain open until identity and stock views agree.',
            ],
        ],
    ],
    'checks-points' => [
        'pageTitle' => 'Checks & Points',
        'eyebrow' => 'Operations / Checks & Points',
        'summary' => 'Operational placeholder for purchases, accrual events, fiscal search, and point adjustments.',
        'nextStep' => 'Add fiscal lookup, accrual history, and shop/date filters.',
        'actions' => [
            ['label' => 'Find receipt', 'tone' => 'primary'],
            ['label' => 'Review accrual gaps', 'tone' => 'secondary'],
        ],
        'metrics' => [
            ['label' => 'Receipts listed', 'value' => '3'],
            ['label' => 'Positive accruals', 'value' => '2'],
            ['label' => 'Zero accruals', 'value' => '1'],
        ],
        'table' => [
            'columns' => ['Receipt', 'Card', 'Shop', 'Amount', 'Points', 'Created'],
            'rows' => [
                ['CHK-90421', 'GX-100001', 'Central Shop', '24,500', '+245', '2026-04-13 18:42'],
                ['CHK-90407', 'GX-100003', 'Central Shop', '11,000', '0', '2026-04-13 14:05'],
                ['CHK-90388', 'GX-100002', 'North Shop', '7,300', '+73', '2026-04-13 10:11'],
            ],
            'filters' => ['Shop', 'Date range', 'Card number', 'Fiscal receipt'],
        ],
        'operationalGlossary' => [
            ['term' => 'Fiscal receipt', 'meaning' => 'The legacy sale lookup key used to trace purchase and accrual events.'],
            ['term' => 'Points', 'meaning' => 'The loyalty delta applied after receipt validation, including zero-accrual outcomes.'],
        ],
        'legacyParityNotes' => [
            'Preserve receipt-first lookup as the main path for operational troubleshooting.',
            'Keep zero-point and positive-point outcomes visible in the same transaction list.',
        ],
        'operationalNextSlice' => [
            'summary' => 'The safest first backend step is a read-only receipt history with simple shop and date filters.',
            'steps' => [
                'Load transaction rows with receipt, card, amount, and points columns.',
                'Add fiscal receipt lookup before any manual point adjustment actions.',
                'Treat accrual correction workflows as a later slice after read parity is proven.',
            ],
        ],
        'operationalDataStatus' => [
            ['label' => 'Current source', 'value' => 'Static preview rows from config/admin-pages.php'],
            ['label' => 'Target source', 'value' => 'Receipt and accrual event records from the future transaction domain'],
            ['label' => 'Blocker', 'value' => 'PHP runtime is unavailable, so real query wiring cannot be validated yet'],
        ],
        'operationalMigrationBlockers' => [
            'PHP runtime is missing, so receipt-history queries and filters cannot be executed locally.',
            'Transaction domain tables do not exist yet, so accrual parity remains structural only.',
        ],
        'notice' => [
            'title' => 'Checks and points operations are still preview-only',
            'description' => 'Receipt lookup actions, accrual metrics, and troubleshooting cues are shaping the final Galaxy flow, but real Laravel transaction reads do not exist yet.',
        ],
        'readinessChecklist' => [
            ['status' => 'ready', 'label' => 'Preview receipt lookup surface and accrual metrics are defined'],
            ['status' => 'ready', 'label' => 'Operational parity cues for receipt-first troubleshooting are visible'],
            ['status' => 'pending', 'label' => 'Transaction tables, receipt reads, and adjustment flows still need PHP-backed Laravel wiring'],
        ],
        'dependencyStatus' => [
            ['label' => 'Domain model', 'value' => 'Transaction domain tables do not exist yet'],
            ['label' => 'Backend dependency', 'value' => 'Receipt history queries and adjustment handlers are still pending'],
            ['label' => 'Operational dependency', 'value' => 'Accrual correction rules and fiscal parity still need legacy verification'],
        ],
        'implementationHandoff' => [
            'summary' => 'When PHP is available, start with a read-only receipt history before attempting manual accrual adjustments.',
            'steps' => [
                'Introduce transaction tables or a read model for receipt and points history.',
                'Add shop, date-range, and receipt lookup filters before any mutation paths.',
                'Defer adjustment and correction actions until receipt parity is proven on live data.',
            ],
        ],
        'activityTimeline' => [
            ['title' => 'Receipt-first lookup preserved', 'time' => 'Today, 14:55', 'description' => 'The preview keeps fiscal receipt search central to match operational troubleshooting habits.'],
            ['title' => 'Zero-accrual case retained', 'time' => 'Yesterday, 11:40', 'description' => 'A zero-points row remains visible so parity is not lost before transaction tables exist.'],
        ],
        'legacyMapping' => [
            ['label' => 'Legacy source', 'value' => 'Old Galaxy checks and points history screen'],
            ['label' => 'Parity focus', 'value' => 'Receipt-first lookup, points delta visibility, troubleshooting flow'],
            ['label' => 'Migration note', 'value' => 'Keep receipt tracing central before introducing correction actions'],
        ],
        'operatorChecklist' => [
            'summary' => 'Legacy receipt troubleshooting depended on a short, repeatable triage pattern before any manual correction was considered.',
            'items' => [
                'Search by fiscal receipt before checking card history.',
                'Inspect zero-accrual receipts before assuming a loyalty failure.',
                'Confirm shop and timestamp context before discussing point corrections.',
            ],
        ],
        'escalationGuide' => [
            'summary' => 'Checks and points issues in the old system were escalated carefully because receipt and loyalty mistakes had different owners.',
            'items' => [
                'Escalate receipt-not-found cases before discussing manual point recovery.',
                'Route repeated zero-accrual complaints to loyalty rule review after receipt validation.',
                'Treat shop timestamp mismatches as fiscal-data issues before operator correction requests.',
            ],
        ],
        'shiftHandoff' => [
            'summary' => 'Receipt troubleshooting in the legacy console used handoff notes to preserve what had already been validated before the next operator picked up the case.',
            'items' => [
                'Carry unresolved receipt-not-found cases with the last verified search inputs.',
                'Note repeated zero-accrual complaints that already passed receipt validation.',
                'Leave shop and timestamp mismatch context attached before any correction discussion continues.',
            ],
        ],
        'openIssues' => [
            'summary' => 'Checks and points work in the old Galaxy flow kept a visible backlog of unresolved receipt and accrual questions across shifts.',
            'items' => [
                'Receipt-not-found cases remain open until fiscal search inputs are exhausted.',
                'Repeated zero-accrual complaints remain open until rule review or receipt evidence resolves them.',
                'Shop and timestamp mismatches remain open until fiscal data is reconciled.',
            ],
        ],
    ],
    'card-types' => [
        'pageTitle' => 'Card Types',
        'eyebrow' => 'Catalog / Card Types',
        'summary' => 'Baseline management screen for Galaxy card tiers, points rules, and activation settings.',
        'nextStep' => 'Replace sample controls with real CRUD handlers and validation.',
        'actions' => [
            ['label' => 'New type', 'tone' => 'primary', 'href' => '#live-form'],
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
        'liveForm' => [
            'title' => 'Create card type in Laravel',
            'description' => 'This is the first real write-oriented Phase 1 form path. Keep it minimal and parity-first while preview controls still cover the richer future workflow.',
            'method' => 'POST',
            'actionRoute' => 'admin.card-types.store',
            'cancelRoute' => 'admin.card-types.index',
            'cancelLabel' => 'Back to catalog',
            'submitLabel' => 'Create card type',
            'fields' => [
                ['name' => 'name', 'label' => 'Type name', 'type' => 'text', 'value' => 'Gold', 'required' => true, 'autofocus' => true, 'placeholder' => 'Galaxy Prime', 'help' => 'Use the operator-facing tier name from the Galaxy catalog.', 'attributes' => ['autocomplete' => 'organization-title']],
                ['name' => 'slug', 'label' => 'Slug', 'type' => 'text', 'value' => 'gold', 'required' => true, 'placeholder' => 'galaxy-prime', 'help' => 'Lowercase identifier used in imports and rule mapping.', 'attributes' => ['autocomplete' => 'off', 'spellcheck' => 'false']],
                ['name' => 'points_rate', 'label' => 'Points rate', 'type' => 'number', 'value' => '1.50', 'required' => true, 'placeholder' => '1.50', 'help' => 'Decimal multiplier applied to spend accrual for this tier.', 'attributes' => ['step' => '0.01', 'min' => '0', 'inputmode' => 'decimal']],
                ['name' => 'is_active', 'label' => 'Status', 'type' => 'select', 'value' => '1', 'required' => true, 'options' => [
                    ['label' => 'Active', 'value' => '1'],
                    ['label' => 'Draft', 'value' => '0'],
                ]],
            ],
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
                ['label' => 'Create first type', 'tone' => 'primary', 'href' => '#live-form'],
            ],
        ],
        'notice' => [
            'title' => 'Card type workflow is partially live',
            'description' => 'A minimal Laravel create path now exists for card types, while the richer tier rules and publish workflow remain preview-only.',
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
            ['status' => 'ready', 'label' => 'Minimal Laravel create path is now wired for card types'],
            ['status' => 'pending', 'label' => 'Tier rule publishing and richer workflow handlers still need PHP-backed follow-through'],
        ],
        'dependencyStatus' => [
            ['label' => 'Domain model', 'value' => 'CardType model and migration skeleton exist'],
            ['label' => 'Backend dependency', 'value' => 'Minimal create wiring now exists, but update flow, publish logic, and rule imports are still pending'],
            ['label' => 'Operational dependency', 'value' => 'Legacy accrual rules need live verification before publish flow is enabled'],
        ],
        'operatorChecklist' => [
            'summary' => 'Keep card-tier changes aligned with the legacy accrual model until real write flows are available.',
            'items' => [
                'Review activation mode before publishing a new or changed tier.',
                'Compare points rate against the legacy card catalog before drafting a replacement type.',
                'Keep approval-based tiers in draft until parity checks are complete.',
            ],
        ],
        'escalationGuide' => [
            'summary' => 'Escalate tier-rule uncertainty before changing a loyalty behavior that customers will feel immediately.',
            'items' => [
                'Escalate activation-rule disagreements before publishing a tier change.',
                'Escalate points-rate mismatches that could alter accrual parity.',
            ],
        ],
        'shiftHandoff' => [
            'summary' => 'Carry unresolved tier-rule questions forward with enough detail to preserve legacy card behavior.',
            'items' => [
                'Hand off draft tiers with the exact legacy rate and activation mode they are meant to mirror.',
                'Note any tier rules still waiting on parity confirmation from operations.',
            ],
        ],
        'openIssues' => [
            'summary' => 'Known card-type migration gaps that still block safe rollout.',
            'items' => [
                'Partner tier approval flow parity is still unresolved against the legacy workflow.',
                'Rule import behavior is still undefined for the first Laravel write slice.',
            ],
        ],
        'implementationHandoff' => [
            'summary' => 'When PHP becomes available, start by turning the card type preview into a real create/update path with the smallest possible write flow.',
            'steps' => [
                'Add a dedicated form request for card type validation.',
                'Create store and update controller actions behind the existing route structure.',
                'Persist a minimal name, slug, rate, and activation mode payload before expanding rule imports.',
            ],
        ],
    ],
    'services-rules' => [
        'pageTitle' => 'Services & Rules',
        'eyebrow' => 'Catalog / Services & Rules',
        'summary' => 'Baseline management screen for service groups, eligibility rules, and business conditions that drive loyalty behavior.',
        'nextStep' => 'Replace sample controls with real rule CRUD, priority ordering, and condition editing.',
        'actions' => [
            [
                'label' => 'New rule',
                'tone' => 'primary',
                'disabled' => true,
                'disabledReason' => 'Blocked until the first Laravel-backed service-rule write flow exists for group, scope, effect, and priority.',
            ],
            ['label' => 'Review priorities', 'tone' => 'secondary', 'disabled' => true, 'disabledReason' => 'Blocked until rule priority resolution is verified in Laravel.'],
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
                ['label' => 'Publish rule', 'tone' => 'primary', 'disabled' => true, 'disabledReason' => 'Blocked until rule CRUD and parity checks exist beyond the preview shell.'],
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
        'operatorChecklist' => [
            'summary' => 'Keep the rules workspace focused on parity-first operational review until real CRUD is available.',
            'items' => [
                'Review priority collisions before drafting a replacement rule.',
                'Confirm shop scope against the legacy rule matrix before publishing changes.',
                'Keep bar-service exclusions in draft until parity review is complete.',
            ],
        ],
        'escalationGuide' => [
            'summary' => 'Route rule conflicts through operations owners before changing customer-facing loyalty behavior.',
            'items' => [
                'Escalate overlapping priority conflicts before introducing a new accrual rule.',
                'Escalate shop-scope disagreements to the loyalty operations owner.',
            ],
        ],
        'shiftHandoff' => [
            'summary' => 'Carry unresolved rule parity work forward with enough context to avoid accidental behavior drift.',
            'items' => [
                'Hand off unresolved priority conflicts with the compared legacy rule names.',
                'Note any shop-scope questions still waiting on operations confirmation.',
            ],
        ],
        'openIssues' => [
            'summary' => 'Known service-rule migration gaps that still block safe rollout.',
            'items' => [
                'Night service block parity is still under review against legacy exclusions.',
                'Priority resolution needs live confirmation before enabling write flows.',
            ],
        ],
        'implementationHandoff' => [
            'summary' => 'When backend work starts, introduce the smallest rule persistence path before attempting full condition-builder parity.',
            'steps' => [
                'Define a first Laravel model and migration for service rules.',
                'Wire a simple store action for rule group, scope, effect, and priority.',
                'Keep advanced condition syntax out of the first implementation slice.',
            ],
        ],
    ],
    'gifts' => [
        'pageTitle' => 'Gifts',
        'eyebrow' => 'Catalog / Gifts',
        'summary' => 'Baseline management screen for gift catalog, redemption settings, and stock-aware reward management.',
        'nextStep' => 'Replace sample controls with real gift CRUD, stock tracking, and redemption flows.',
        'actions' => [
            [
                'label' => 'New gift',
                'tone' => 'primary',
                'disabled' => true,
                'disabledReason' => 'Blocked until the first Laravel-backed gift write flow exists for catalog, scope, cost, and stock state.',
            ],
            ['label' => 'Stock audit', 'tone' => 'secondary', 'disabled' => true, 'disabledReason' => 'Blocked until stock checks are backed by Laravel inventory data.'],
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
                ['label' => 'Publish gift', 'tone' => 'primary', 'disabled' => true, 'disabledReason' => 'Blocked until gift CRUD and redemption parity exist beyond the preview shell.'],
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
        'operatorChecklist' => [
            'summary' => 'Keep the gift catalog aligned with redemption reality while the workflow is still preview-only.',
            'items' => [
                'Review zero-stock rewards before reopening a paused gift.',
                'Confirm shop scope against the legacy reward catalog before publishing changes.',
                'Check points cost parity before drafting a replacement reward.',
            ],
        ],
        'escalationGuide' => [
            'summary' => 'Escalate stock and redemption mismatches before changing a live-facing reward offer.',
            'items' => [
                'Escalate stock discrepancies before reactivating a paused reward.',
                'Escalate point-cost disagreements to the loyalty operations owner before publish review.',
            ],
        ],
        'shiftHandoff' => [
            'summary' => 'Carry unresolved gift-catalog risks forward with enough detail to preserve legacy redemption behavior.',
            'items' => [
                'Hand off paused rewards with the latest stock assumption and shop scope.',
                'Note any rewards still waiting on parity confirmation for point cost or availability.',
            ],
        ],
        'openIssues' => [
            'summary' => 'Known gift migration gaps that still block safe rollout.',
            'items' => [
                'Premium dessert set remains paused until zero-stock parity is confirmed.',
                'Warehouse synchronization is still undefined for the first Laravel write slice.',
            ],
        ],
        'implementationHandoff' => [
            'summary' => 'When PHP is available, begin with basic gift CRUD and defer stock synchronization until after the first write path works.',
            'steps' => [
                'Create a gift model and migration with name, cost, scope, and stock fields.',
                'Wire a minimal create and edit form flow through Laravel validation.',
                'Treat warehouse sync and redemption logs as a later follow-up slice.',
            ],
        ],
    ],
    'roles-permissions' => [
        'pageTitle' => 'Roles & Permissions',
        'eyebrow' => 'Administration / Roles & Permissions',
        'summary' => 'Baseline management screen for admin roles, permission bundles, and future shop-scoped access rules.',
        'nextStep' => 'Replace sample controls with real role assignment, permission matrix, and shop-aware policy flows.',
        'actions' => [
            ['label' => 'New role', 'tone' => 'primary'],
            ['label' => 'Review matrix', 'tone' => 'secondary', 'disabled' => true, 'disabledReason' => 'Blocked until the Laravel permission matrix can be verified against legacy staff access.'],
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
                ['label' => 'Publish role', 'tone' => 'primary', 'disabled' => true, 'disabledReason' => 'Blocked until role persistence and shop-scoped parity checks exist beyond the preview shell.'],
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
        'operatorChecklist' => [
            'summary' => 'Keep access changes tightly aligned with the old Galaxy staff model until real authorization flows are live.',
            'items' => [
                'Review shop scope before publishing a manager or cashier role change.',
                'Compare permission bundles against the legacy staff matrix before drafting a new role.',
                'Keep assignment-sensitive roles in draft until shop-scoped behavior is verified.',
            ],
        ],
        'escalationGuide' => [
            'summary' => 'Escalate access-boundary uncertainty before changing live-facing staff permissions.',
            'items' => [
                'Escalate shop-scope disagreements before changing a role bundle.',
                'Escalate permission gaps that could block cashier or manager workflows.',
            ],
        ],
        'shiftHandoff' => [
            'summary' => 'Carry unresolved access and assignment questions forward with enough detail to preserve legacy staff behavior.',
            'items' => [
                'Hand off draft roles with the exact legacy bundle they are meant to mirror.',
                'Note any assignment rules still waiting on shop-scoped parity confirmation.',
            ],
        ],
        'openIssues' => [
            'summary' => 'Known access-control migration gaps that still block safe rollout.',
            'items' => [
                'Cashier assignment rules remain unverified against legacy shop-scoped behavior.',
                'Permission matrix editing is still undefined for the first Laravel write slice.',
            ],
        ],
        'implementationHandoff' => [
            'summary' => 'Once PHP-backed flows are possible, start with a minimal role create/update path before exposing full assignment screens.',
            'steps' => [
                'Add a role form request for name, scope, and status validation.',
                'Persist a minimal role record before tackling permission matrix editing.',
                'Layer shop assignments and policy checks in after the first save flow is stable.',
            ],
        ],
    ],
    'reports' => [
        'pageTitle' => 'Reports',
        'eyebrow' => 'Administration / Reports',
        'summary' => 'Operational placeholder for analytics, histories, and export-oriented admin reporting.',
        'nextStep' => 'Add report catalog, date-range presets, and export entry points.',
        'actions' => [
            ['label' => 'Open report catalog', 'tone' => 'primary'],
            ['label' => 'Review export presets', 'tone' => 'secondary'],
        ],
        'metrics' => [
            ['label' => 'Planned reports', 'value' => '3'],
            ['label' => 'Export formats', 'value' => '3'],
            ['label' => 'Preset periods', 'value' => '3'],
        ],
        'table' => [
            'columns' => ['Report', 'Scope', 'Default period', 'Format', 'Status'],
            'rows' => [
                ['Points accrual summary', 'All shops', 'Last 30 days', 'XLSX', 'planned'],
                ['Card activity history', 'Per shop', 'Last 7 days', 'Table', 'planned'],
                ['Gift redemption report', 'All shops', 'Month to date', 'CSV', 'planned'],
            ],
            'filters' => ['Shop scope', 'Period preset', 'Report type'],
        ],
        'operationalGlossary' => [
            ['term' => 'Default period', 'meaning' => 'The initial reporting window that should match the old Galaxy operator defaults.'],
            ['term' => 'Format', 'meaning' => 'The first delivery mode for exports or on-screen analytics views.'],
        ],
        'legacyParityNotes' => [
            'Start with operator-friendly default periods before adding advanced analytics filters.',
            'Preserve export-first reporting habits from the old Galaxy back office.',
        ],
        'operationalNextSlice' => [
            'summary' => 'When reporting work begins, start with a simple catalog of report entry points and fixed presets.',
            'steps' => [
                'Render report definitions from config or a lightweight service first.',
                'Add preset period handling before custom builders or exports.',
                'Treat heavy analytics and generated files as later implementation slices.',
            ],
        ],
        'operationalDataStatus' => [
            ['label' => 'Current source', 'value' => 'Static preview rows from config/admin-pages.php'],
            ['label' => 'Target source', 'value' => 'Report catalog definitions plus later query-backed metrics sources'],
            ['label' => 'Blocker', 'value' => 'PHP runtime is unavailable, so real query wiring cannot be validated yet'],
        ],
        'operationalMigrationBlockers' => [
            'PHP runtime is missing, so preset handling and report catalog wiring cannot be validated locally.',
            'Underlying analytics sources do not exist yet, so reports remain catalog-only previews.',
        ],
        'notice' => [
            'title' => 'Reporting operations are still preview-only',
            'description' => 'Catalog actions, summary metrics, and export cues are outlining the Galaxy reporting workspace, but no Laravel reporting pipeline is wired yet.',
        ],
        'readinessChecklist' => [
            ['status' => 'ready', 'label' => 'Preview report catalog actions and preset metrics are defined'],
            ['status' => 'ready', 'label' => 'Operational parity cues for export-first reporting are visible'],
            ['status' => 'pending', 'label' => 'Real report sources, presets, and exports still need PHP-backed Laravel wiring'],
        ],
        'dependencyStatus' => [
            ['label' => 'Domain model', 'value' => 'Report catalog is still config-backed with no reporting domain service yet'],
            ['label' => 'Backend dependency', 'value' => 'Preset handling, query sources, and export pipeline are still pending'],
            ['label' => 'Operational dependency', 'value' => 'Legacy report presets and export expectations still need live verification'],
        ],
        'implementationHandoff' => [
            'summary' => 'When PHP is available, start with a simple report catalog and fixed presets before building exports or analytics pipelines.',
            'steps' => [
                'Move report definitions into a lightweight service or queryable catalog source.',
                'Add preset period handling before custom ranges or heavy analytics.',
                'Defer export generation until the basic catalog and preset flow is stable.',
            ],
        ],
        'activityTimeline' => [
            ['title' => 'Points accrual summary kept first', 'time' => 'Today, 10:35', 'description' => 'The report ordering keeps the most common legacy export entry point at the top.'],
            ['title' => 'Gift redemption report retained', 'time' => 'Yesterday, 09:50', 'description' => 'The preview catalog keeps a redemption-oriented export case visible for parity planning.'],
        ],
        'legacyMapping' => [
            ['label' => 'Legacy source', 'value' => 'Old Galaxy reporting catalog'],
            ['label' => 'Parity focus', 'value' => 'Preset periods, export-first habits, report ordering'],
            ['label' => 'Migration note', 'value' => 'Preserve common export entry points before adding heavier analytics'],
        ],
        'operatorChecklist' => [
            'summary' => 'Reporting in the old Galaxy admin was usually a quick export task driven by preset periods and familiar entry points.',
            'items' => [
                'Start with preset periods before opening custom date-range work.',
                'Keep the highest-frequency export categories at the top of the catalog.',
                'Validate export format expectations before adding heavier analytics views.',
            ],
        ],
        'escalationGuide' => [
            'summary' => 'Legacy reporting work escalated only when preset exports could not satisfy the operational request.',
            'items' => [
                'Escalate missing preset coverage before promising custom analytics.',
                'Route export-format disputes to reporting owners before adding new catalog entries.',
                'Treat repeated ad hoc requests as a catalog-gap signal for later Phase work.',
            ],
        ],
        'shiftHandoff' => [
            'summary' => 'Reporting continuity in the old Galaxy admin depended on handoff notes that captured which export need was still open and which preset had already been tried.',
            'items' => [
                'Carry unmet preset requests into the next shift before inventing custom exports.',
                'Note unresolved export-format disputes so the next operator does not repeat the same catalog path.',
                'Flag repeated ad hoc asks that should become future report catalog entries.',
            ],
        ],
        'openIssues' => [
            'summary' => 'The legacy reporting catalog usually carried a short backlog of unresolved export needs that stayed visible between operators.',
            'items' => [
                'Missing preset coverage remains open until the reporting owner confirms a supported path.',
                'Export-format disputes remain open until one format expectation is chosen.',
                'Repeated ad hoc requests remain open as signals for future report catalog expansion.',
            ],
        ],
    ],
];
