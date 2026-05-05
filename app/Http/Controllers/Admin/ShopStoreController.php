<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RedirectsToSelectedShopContext;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreShopRequest;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;

class ShopStoreController extends Controller
{
    use RedirectsToSelectedShopContext;

    public function __invoke(StoreShopRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $shop = Shop::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'is_active' => $request->boolean('is_active'),
            'review_note' => $validated['review_note'] ?? null,
        ]);

        return $this->redirectToSelectedShop(
            $shop,
            sprintf('Shop "%s" was created.', $shop->name),
        );
    }
}
