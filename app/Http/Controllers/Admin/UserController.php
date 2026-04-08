<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

    public function create(): View
    {
        return view('admin.users.create', [
            'pageTitle' => 'Create user',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'is_admin' => ['nullable', 'boolean'],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        if ($request->boolean('is_admin')) {
            $user->forceFill(['is_admin' => true])->save();
        }

        return redirect()->route('admin.users.show', $user);
    }

    public function show(User $user): View
    {
        return view('admin.users.show', [
            'pageTitle' => 'User details',
            'user' => $user,
        ]);
    }
}
