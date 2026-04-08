<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->select(['id', 'name', 'email', 'is_admin', 'created_at'])
            ->latest('created_at')
            ->get();

        return view('admin.users.index', [
            'pageTitle' => 'Users',
            'users' => $users,
        ]);
    }

    public function show(User $user): View
    {
        return view('admin.users.show', [
            'pageTitle' => 'User details',
            'user' => $user,
        ]);
    }
}
