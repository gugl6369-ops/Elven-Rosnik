<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::loginView(
            fn () => Inertia::render('auth/ui/AuthPage', ['mode' => 'login']),
        );

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::query()
                ->where('username', $request->input('username'))
                ->first();

            if (! $user) {
                return null;
            }

            if ($user->is_blocked ?? false) {
                return null;
            }

            return Hash::check($request->password, $user->password)
                ? $user
                : null;
        });

        if (config('auth_throttle.disabled')) {
            RateLimiter::for('login', fn () => Limit::none());
            RateLimiter::for('registration', fn () => Limit::none());
        } else {
            RateLimiter::for('login', function (Request $request) {
                $throttleKey = Str::transliterate(
                    Str::lower($request->input('username')).'|'.$request->ip(),
                );

                return Limit::perMinute(5)->by($throttleKey);
            });

            RateLimiter::for('registration', function (Request $request) {
                return Limit::perMinute(5)->by($request->ip());
            });
        }
    }
}