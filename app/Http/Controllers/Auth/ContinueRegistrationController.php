<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\CompleteUserRegistration;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ContinueRegistrationController extends Controller
{
    public function show(Request $request): Response|RedirectResponse
    {
        if ($request->user()) {
            return redirect()->route('home');
        }

        return Inertia::render('auth/ui/ContinueRegisterPage');
    }

    public function store(
        Request $request,
        CompleteUserRegistration $completeUserRegistration,
    ): RedirectResponse {
        if ($request->user()) {
            return redirect()->route('home');
        }

        $input = $request->all();
        $input['avatar'] = $request->file('avatar');

        $user = $completeUserRegistration->create($input);

        Auth::login($user);

        return redirect()->route('home');
    }
}