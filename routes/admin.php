<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ResourceIndexController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->middleware(['auth', 'can:access-admin'])
    ->as('admin.')
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/shops', ResourceIndexController::class)->defaults('resource', 'shops')->name('shops.index');
        Route::get('/cardholders', ResourceIndexController::class)->defaults('resource', 'cardholders')->name('cardholders.index');
        Route::get('/cards', ResourceIndexController::class)->defaults('resource', 'cards')->name('cards.index');
        Route::get('/card-types', ResourceIndexController::class)->defaults('resource', 'card-types')->name('card-types.index');
        Route::get('/roles-permissions', ResourceIndexController::class)->defaults('resource', 'roles-permissions')->name('roles-permissions.index');
    });
