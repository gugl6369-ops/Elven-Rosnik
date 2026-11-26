<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    public function show(Request $request): Response|RedirectResponse
    {
        if ($request->user()) {
            return redirect()->route('home');
        }

        return Inertia::render('auth/ui/AuthPage', ['mode' => 'register']);
    }
}