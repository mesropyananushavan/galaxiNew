<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RedirectsToSelectedCardContext;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCardRequest;
use App\Models\Card;
use Illuminate\Http\RedirectResponse;

class CardStoreController extends Controller
{
    use RedirectsToSelectedCardContext;

    public function __invoke(StoreCardRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $card = Card::create([
            'shop_id' => $validated['shop_id'],
            'card_type_id' => $validated['card_type_id'],
            'number' => $validated['number'],
            'status' => $validated['status'],
            'activated_at' => $validated['activated_at'] ?? null,
            'review_note' => $validated['review_note'] ?? null,
        ]);

        return $this->redirectToSelectedCard(
            $card,
            sprintf('Card "%s" was created.', $card->number),
        );
    }
}
