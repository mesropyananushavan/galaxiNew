<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RedirectsToSelectedCardContext;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCardRequest;
use App\Models\Card;
use Illuminate\Http\RedirectResponse;

class CardUpdateController extends Controller
{
    use RedirectsToSelectedCardContext;

    public function __invoke(UpdateCardRequest $request, Card $card): RedirectResponse
    {
        $validated = $request->validated();

        $card->update([
            'shop_id' => $validated['shop_id'],
            'card_type_id' => $validated['card_type_id'],
            'number' => $validated['number'],
            'status' => $validated['status'],
            'issued_at' => $validated['issued_at'] ?? null,
            'activated_at' => $validated['activated_at'] ?? null,
            'review_note' => $validated['review_note'] ?? null,
        ]);

        return $this->redirectToSelectedCard(
            $card,
            sprintf('Card "%s" was updated.', $card->number),
        );
    }
}
