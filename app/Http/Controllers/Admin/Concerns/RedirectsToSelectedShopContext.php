<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Shop;
use Illuminate\Http\RedirectResponse;

trait RedirectsToSelectedShopContext
{
    protected function redirectToSelectedShop(Shop $shop, string $status): RedirectResponse
    {
        return redirect()
            ->to(route('admin.shops.index', ['shop' => $shop], absolute: false).'#backend-flow-status')
            ->with('status', $status);
    }
}
