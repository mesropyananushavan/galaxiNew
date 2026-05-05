<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RedirectsToSelectedCardHolderContext;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCardHolderRequest;
use App\Models\CardHolder;
use Illuminate\Http\RedirectResponse;

class CardHolderUpdateController extends Controller
{
    use RedirectsToSelectedCardHolderContext;

    public function __invoke(UpdateCardHolderRequest $request, CardHolder $cardholder): RedirectResponse
    {
        $validated = $request->validated();

        $cardholder->update([
            'shop_id' => $validated['shop_id'],
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'review_note' => $validated['review_note'] ?? null,
        ]);

        return $this->redirectToSelectedCardHolder(
            $cardholder,
            sprintf('Cardholder "%s" was updated.', $cardholder->full_name),
        );
    }
}
