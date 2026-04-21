<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RedirectsToSelectedCardTypeContext;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCardTypeRequest;
use App\Models\CardType;
use Illuminate\Http\RedirectResponse;

class CardTypeUpdateController extends Controller
{
    use RedirectsToSelectedCardTypeContext;

    public function __invoke(UpdateCardTypeRequest $request, CardType $cardType): RedirectResponse
    {
        $validated = $request->validated();

        $cardType->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'points_rate' => $validated['points_rate'],
            'is_active' => $request->boolean('is_active'),
            'review_note' => $validated['review_note'] ?? null,
        ]);

        return $this->redirectToSelectedCardType(
            $cardType,
            sprintf('Card type "%s" was updated.', $cardType->name),
        );
    }
}
