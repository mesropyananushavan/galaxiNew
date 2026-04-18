<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CardType;
use Illuminate\Http\RedirectResponse;

class CardTypeToggleStatusController extends Controller
{
    public function __invoke(CardType $cardType): RedirectResponse
    {
        $cardType->update([
            'is_active' => ! $cardType->is_active,
        ]);

        return redirect()
            ->to(route('admin.card-types.index', ['cardType' => $cardType], absolute: false).'#backend-flow-status')
            ->with('status', sprintf(
                'Card type "%s" is now %s.',
                $cardType->name,
                $cardType->is_active ? 'active' : 'draft'
            ));
    }
}
