<?php

return [
    'focus' => 'Keep the first Galaxy schema anchors explicit while Phase 1 turns starter-era tables into a branch, access, holder, and card foundation.',
    'guide' => ['docs/phase-1-migration-baseline.md', 'config/phase-1-migration-baseline.php'],
    'source_of_truth' => ['docs/phase-1-migration-baseline.md', 'config/phase-1-migration-baseline.php', 'database/migrations'],
    'posture' => 'documented migration baseline for the live Galaxy schema layer',
    'items' => [
        [
            'label' => 'Foundation access schema',
            'migration' => '2026_04_14_084500_create_foundation_access_tables.php',
            'coverage' => 'Introduces Galaxy branch and access tables plus the first role and permission linkage anchors.',
        ],
        [
            'label' => 'Card domain schema',
            'migration' => '2026_04_14_090000_create_card_domain_tables.php',
            'coverage' => 'Introduces holder, tier, and card shell tables for the first loyalty-domain data layer.',
        ],
        [
            'label' => 'Review and activation follow-ups',
            'migration' => '2026_04_21_190500_add_is_active_to_roles_table.php + 2026_04_21_213600_add_review_note_to_roles_table.php + 2026_04_21_222200_add_review_note_to_card_types_table.php + 2026_04_22_000500_add_activation_note_to_card_types_table.php + 2026_04_22_002000_add_access_note_to_roles_table.php + 2026_04_22_011200_add_assignment_note_to_roles_table.php + 2026_04_22_014200_add_rollout_note_to_card_types_table.php',
            'coverage' => 'Extends the first schema layer with review-state and rollout-state anchors for access and tier review.',
        ],
        [
            'label' => 'Branch and inventory review follow-ups',
            'migration' => '2026_05_05_103200_add_review_note_to_shops_and_card_holders_tables.php + 2026_05_05_104700_add_review_note_to_cards_table.php + 2026_05_05_110200_add_review_note_to_permissions_table.php + 2026_05_06_043500_add_issued_at_to_cards_table.php',
            'coverage' => 'Extends branch, holder, card, and permission tables with review and issuance anchors used by the live Galaxy foundation layer.',
        ],
    ],
];
