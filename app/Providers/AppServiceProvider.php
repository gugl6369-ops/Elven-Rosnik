<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Plant;
use App\Models\Recipe;
use App\Policies\CommentPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('web', fn (Request $request) => Limit::perMinute(600)
            ->by('ip:'.$request->ip()));

        RateLimiter::for('catalog', fn (Request $request) => Limit::perMinute(90)
            ->by('ip:'.$request->ip()));

        RateLimiter::for('feed', fn (Request $request) => Limit::perMinute(60)
            ->by('ip:'.$request->ip()));

        RateLimiter::for('mutations', function (Request $request) {
            $ipLimit = Limit::perMinute(90)->by('ip:'.$request->ip());
            $userId = $request->user()?->getAuthIdentifier();

            return $userId === null
                ? $ipLimit
                : [Limit::perMinute(30)->by('user:'.$userId), $ipLimit];
        });

        RateLimiter::for('sensitive', function (Request $request) {
            $ipLimit = Limit::perMinute(20)->by('ip:'.$request->ip());
            $userId = $request->user()?->getAuthIdentifier();

            return $userId === null
                ? $ipLimit
                : [Limit::perMinute(5)->by('user:'.$userId), $ipLimit];
        });

        RateLimiter::for('uploads', function (Request $request) {
            $ipLimit = Limit::perMinute(20)->by('ip:'.$request->ip());
            $userId = $request->user()?->getAuthIdentifier();

            return $userId === null
                ? $ipLimit
                : [Limit::perMinute(5)->by('user:'.$userId), $ipLimit];
        });

        RateLimiter::for('admin', function (Request $request) {
            $ipLimit = Limit::perMinute(240)->by('ip:'.$request->ip());
            $userId = $request->user()?->getAuthIdentifier();

            return $userId === null
                ? $ipLimit
                : [Limit::perMinute(120)->by('user:'.$userId), $ipLimit];
        });

        Relation::enforceMorphMap([
            'plant' => Plant::class,
            'recipe' => Recipe::class,
        ]);

        Gate::policy(Comment::class, CommentPolicy::class);

        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
