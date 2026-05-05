<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\CardHolder;
use Illuminate\Http\RedirectResponse;

trait RedirectsToSelectedCardHolderContext
{
    protected function redirectToSelectedCardHolder(CardHolder $cardHolder, string $status): RedirectResponse
    {
        return redirect()
            ->to(route('admin.cardholders.index', ['cardholder' => $cardHolder], absolute: false).'#backend-flow-status')
            ->with('status', $status);
    }
}
