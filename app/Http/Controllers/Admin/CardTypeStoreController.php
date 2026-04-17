<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCardTypeRequest;
use App\Models\CardType;
use Illuminate\Http\RedirectResponse;

class CardTypeStoreController extends Controller
{
    public function __invoke(StoreCardTypeRequest $request): RedirectResponse
    {
        $cardType = CardType::create($request->validated());

        return redirect()
            ->route('admin.card-types.index')
            ->with('status', sprintf('Card type "%s" was created.', $cardType->name));
    }
}
