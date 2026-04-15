<?php

return [
    // `resourceBlocks` intentionally flows through the bridge config so layered shell
    // changes stay centralized in config/admin-resource-blocks.php.
    'phase' => 1,
    'resourceBlocks' => config('admin-resource-blocks', []),
    'pageRationale' => config('admin-page-rationale', []),
];
