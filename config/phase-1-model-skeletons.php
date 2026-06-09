<?php

return [
    'focus' => 'Keep the first Galaxy Eloquent models and migration anchors explicit while Phase 1 turns scaffold data structures into a real branch, access, holder, and card foundation.',
    'guide' => ['docs/phase-1-model-skeletons.md', 'config/phase-1-model-skeletons.php'],
    'source_of_truth' => ['docs/phase-1-model-skeletons.md', 'config/phase-1-model-skeletons.php', 'app/Models', 'database/migrations'],
    'posture' => 'documented model-and-migration baseline for the live Galaxy foundation layer',
    'items' => [
        [
            'label' => 'Shop skeleton',
            'model' => 'app/Models/Shop.php',
            'migration' => '2026_04_14_084500_create_foundation_access_tables.php',
            'coverage' => 'Branch identity, activation state, and relationships to staff, holders, and cards.',
        ],
        [
            'label' => 'Role and permission skeletons',
            'model' => 'app/Models/Role.php + app/Models/Permission.php',
            'migration' => '2026_04_14_084500_create_foundation_access_tables.php',
            'coverage' => 'Galaxy access shells, permission vocabulary, and role linkage for admin access review.',
        ],
        [
            'label' => 'CardHolder skeleton',
            'model' => 'app/Models/CardHolder.php',
            'migration' => '2026_04_14_090000_create_card_domain_tables.php',
            'coverage' => 'Branch-aware holder identity plus holder-to-card linkage.',
        ],
        [
            'label' => 'CardType skeleton',
            'model' => 'app/Models/CardType.php',
            'migration' => '2026_04_14_090000_create_card_domain_tables.php',
            'coverage' => 'Tier identity, activation state, and card-type relationship coverage.',
        ],
        [
            'label' => 'Card skeleton',
            'model' => 'app/Models/Card.php',
            'migration' => '2026_04_14_090000_create_card_domain_tables.php + 2026_05_06_043500_add_issued_at_to_cards_table.php',
            'coverage' => 'Card shell inventory linked to holders, tiers, branch scope, and issue/activation timestamps.',
        ],
    ],
];
