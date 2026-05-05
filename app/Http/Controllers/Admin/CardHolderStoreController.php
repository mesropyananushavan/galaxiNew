<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RedirectsToSelectedCardHolderContext;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCardHolderRequest;
use App\Models\CardHolder;
use Illuminate\Http\RedirectResponse;

class CardHolderStoreController extends Controller
{
    use RedirectsToSelectedCardHolderContext;

    public function __invoke(StoreCardHolderRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $cardHolder = CardHolder::create([
            'shop_id' => $validated['shop_id'],
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'review_note' => $validated['review_note'] ?? null,
        ]);

        return $this->redirectToSelectedCardHolder(
            $cardHolder,
            sprintf('Cardholder "%s" was created.', $cardHolder->full_name),
        );
    }
}
