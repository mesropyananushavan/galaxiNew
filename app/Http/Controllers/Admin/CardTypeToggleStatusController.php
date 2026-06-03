<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RedirectsToSelectedCardTypeContext;
use App\Http\Controllers\Controller;
use App\Models\CardType;
use Illuminate\Http\RedirectResponse;

class CardTypeToggleStatusController extends Controller
{
    use RedirectsToSelectedCardTypeContext;

    public function __invoke(CardType $cardType): RedirectResponse
    {
        $cardType->update([
            'is_active' => ! $cardType->is_active,
        ]);

        return $this->redirectToSelectedCardType(
            $cardType,
            sprintf(
                'Card type "%s" is now %s.',
                $cardType->name,
                $this->cardTypeStatusValue($cardType)
            ),
        );
    }

    private function cardTypeIsActive(CardType $cardType): bool
    {
        return $cardType->is_active;
    }

    private function cardTypeStatusValue(CardType $cardType): string
    {
        return $this->cardTypeIsActive($cardType) ? 'active' : 'draft';
    }
}
