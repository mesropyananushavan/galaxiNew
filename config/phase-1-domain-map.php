<?php

return [
    'focus' => 'Keep the first Galaxy foundation entities explicit while Phase 1 replaces scaffold defaults with branch, access, holder, card, and tier-aware building blocks.',
    'guide' => ['docs/phase-1-domain-map.md', 'config/phase-1-domain-map.php'],
    'source_of_truth' => ['docs/phase-1-domain-map.md', 'config/phase-1-domain-map.php'],
    'posture' => 'documented entity baseline for live foundation work',
    'entities' => [
        [
            'label' => 'Shop',
            'table' => 'shops',
            'model' => \App\Models\Shop::class,
            'coverage' => 'Branch scope, activation state, review-note coverage, and operator anchoring.',
        ],
        [
            'label' => 'Role',
            'table' => 'roles',
            'model' => \App\Models\Role::class,
            'coverage' => 'Access shell identity plus review, access-note, and assignment-note anchors for Galaxy staff flows.',
        ],
        [
            'label' => 'Permission',
            'table' => 'permissions',
            'model' => \App\Models\Permission::class,
            'coverage' => 'Permission vocabulary plus review-note coverage attached to live access shells.',
        ],
        [
            'label' => 'CardHolder',
            'table' => 'card_holders',
            'model' => \App\Models\CardHolder::class,
            'coverage' => 'Branch-aware holder identity and status review.',
        ],
        [
            'label' => 'CardType',
            'table' => 'card_types',
            'model' => \App\Models\CardType::class,
            'coverage' => 'Tier catalog identity, active-state baseline, and review, activation, and rollout anchors.',
        ],
        [
            'label' => 'Card',
            'table' => 'cards',
            'model' => \App\Models\Card::class,
            'coverage' => 'Card shell inventory linked to holders, tiers, and branches, with review and issuance anchors.',
        ],
    ],
];
