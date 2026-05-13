<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RedirectsToSelectedCardTypeContext;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCardTypeRequest;
use App\Models\CardType;
use Illuminate\Http\RedirectResponse;

class CardTypeStoreController extends Controller
{
    use RedirectsToSelectedCardTypeContext;

    public function __invoke(StoreCardTypeRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $cardType = CardType::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'points_rate' => $validated['points_rate'],
            'is_active' => $request->boolean('is_active'),
            'review_note' => $validated['review_note'] ?? null,
            'activation_note' => $validated['activation_note'] ?? null,
            'rollout_note' => $validated['rollout_note'] ?? null,
        ]);

        return $this->redirectToSelectedCardType(
            $cardType,
            sprintf('Card type "%s" was created.', $cardType->name),
        );
    }
}
