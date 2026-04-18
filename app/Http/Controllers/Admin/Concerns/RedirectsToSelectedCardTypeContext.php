<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\CardType;
use Illuminate\Http\RedirectResponse;

trait RedirectsToSelectedCardTypeContext
{
    protected function redirectToSelectedCardType(CardType $cardType, string $status): RedirectResponse
    {
        return redirect()
            ->to(route('admin.card-types.index', ['cardType' => $cardType], absolute: false).'#backend-flow-status')
            ->with('status', $status);
    }
}
