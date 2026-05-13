<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Card;
use Illuminate\Http\RedirectResponse;

trait RedirectsToSelectedCardContext
{
    protected function redirectToSelectedCard(Card $card, string $status): RedirectResponse
    {
        return redirect()
            ->to(route('admin.cards.index', ['card' => $card], absolute: false).'#backend-flow-status')
            ->with('status', $status);
    }
}
