<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RedirectsToSelectedCardTypeContext;
use App\Http\Controllers\Controller;
use App\Models\CardType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CardTypeToggleStatusController extends Controller
{
    use RedirectsToSelectedCardTypeContext;

    public function __invoke(Request $request, CardType $cardType): RedirectResponse
    {
        abort_unless($request->user()?->hasBootstrapAdminAccess(), 403);

        $cardType->update([
            'is_active' => ! $cardType->is_active,
        ]);

        return $this->redirectToSelectedCardType(
            $cardType,
            sprintf(
                'Card type "%s" is now %s.',
                $cardType->name,
                $cardType->is_active ? 'active' : 'draft'
            ),
        );
    }
}
