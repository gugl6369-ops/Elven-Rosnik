<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    /**
     * @return list<string>
     */
    public static function inertiaUserFields(): array
    {
        return [
            'id',
            'username',
            'name',
            'email',
            'avatar',
            'status',
            'is_admin',
            'pole_id',
            'created_at',
        ];
    }

    public function show(Request $request): Response
    {
        return Inertia::render('account/ui/AccountPage', [
            'user' => $request->user()->only(self::inertiaUserFields()),
        ]);
    }
}