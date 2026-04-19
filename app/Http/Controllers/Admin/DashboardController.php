<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardHolder;
use App\Models\Role;
use App\Models\Shop;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $navigation = config('admin-navigation');

        return view('admin.dashboard', [
            'pageTitle' => 'Dashboard',
            'navigationGroups' => $navigation,
            'plannedSectionCount' => collect($navigation)->sum(fn (array $group): int => count($group['items'])),
            'shopCount' => Shop::query()->count(),
            'cardHolderCount' => CardHolder::query()->count(),
            'cardCount' => Card::query()->count(),
            'roleCount' => Role::query()->count(),
        ]);
    }
}
