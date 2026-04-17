<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCardTypeRequest;
use App\Models\CardType;
use Illuminate\Http\RedirectResponse;

class CardTypeUpdateController extends Controller
{
    public function __invoke(UpdateCardTypeRequest $request, CardType $cardType): RedirectResponse
    {
        $cardType->update($request->validated());

        return redirect()
            ->to(route('admin.card-types.index', absolute: false).'#backend-flow-status')
            ->with('status', sprintf('Card type "%s" was updated.', $cardType->name));
    }
}
