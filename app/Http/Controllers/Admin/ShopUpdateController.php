<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RedirectsToSelectedShopContext;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateShopRequest;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;

class ShopUpdateController extends Controller
{
    use RedirectsToSelectedShopContext;

    public function __invoke(UpdateShopRequest $request, Shop $shop): RedirectResponse
    {
        $validated = $request->validated();

        $shop->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'is_active' => $request->boolean('is_active'),
            'review_note' => $validated['review_note'] ?? null,
        ]);

        return $this->redirectToSelectedShop(
            $shop,
            sprintf('Shop "%s" was updated.', $shop->name),
        );
    }
}
