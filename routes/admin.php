<?php

use App\Http\Controllers\Admin\CardTypeStoreController;
use App\Http\Controllers\Admin\CardTypeToggleStatusController;
use App\Http\Controllers\Admin\CardTypeUpdateController;
use App\Http\Controllers\Admin\CardHolderStoreController;
use App\Http\Controllers\Admin\CardHolderUpdateController;
use App\Http\Controllers\Admin\CardStoreController;
use App\Http\Controllers\Admin\CardUpdateController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleStoreController;
use App\Http\Controllers\Admin\RoleUpdateController;
use App\Http\Controllers\Admin\ResourceIndexController;
use App\Http\Controllers\Admin\ShopStoreController;
use App\Http\Controllers\Admin\ShopUpdateController;
use App\Models\Shop;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->middleware(['auth', 'can:access-admin'])
    ->as('admin.')
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/shops', ResourceIndexController::class)
            ->middleware('can:viewAny,'.Shop::class)
            ->defaults('resource', 'shops')
            ->name('shops.index');
        Route::post('/shops', ShopStoreController::class)->name('shops.store');
        Route::patch('/shops/{shop}', ShopUpdateController::class)->name('shops.update');
        Route::get('/cardholders', ResourceIndexController::class)->defaults('resource', 'cardholders')->name('cardholders.index');
        Route::post('/cardholders', CardHolderStoreController::class)->name('cardholders.store');
        Route::patch('/cardholders/{cardholder}', CardHolderUpdateController::class)->name('cardholders.update');
        Route::get('/cards', ResourceIndexController::class)->defaults('resource', 'cards')->name('cards.index');
        Route::post('/cards', CardStoreController::class)->name('cards.store');
        Route::patch('/cards/{card}', CardUpdateController::class)->name('cards.update');
        Route::get('/checks-points', ResourceIndexController::class)->defaults('resource', 'checks-points')->name('checks-points.index');
        Route::get('/card-types', ResourceIndexController::class)->defaults('resource', 'card-types')->name('card-types.index');
        Route::post('/card-types', CardTypeStoreController::class)->name('card-types.store');
        Route::patch('/card-types/{cardType}', CardTypeUpdateController::class)->name('card-types.update');
        Route::patch('/card-types/{cardType}/toggle-status', CardTypeToggleStatusController::class)->name('card-types.toggle-status');
        Route::get('/services-rules', ResourceIndexController::class)->defaults('resource', 'services-rules')->name('services-rules.index');
        Route::get('/gifts', ResourceIndexController::class)->defaults('resource', 'gifts')->name('gifts.index');
        Route::get('/roles-permissions', ResourceIndexController::class)->defaults('resource', 'roles-permissions')->name('roles-permissions.index');
        Route::post('/roles-permissions', RoleStoreController::class)->name('roles-permissions.store');
        Route::patch('/roles-permissions/{role}', RoleUpdateController::class)->name('roles-permissions.update');
        Route::get('/reports', ResourceIndexController::class)->defaults('resource', 'reports')->name('reports.index');
    });
