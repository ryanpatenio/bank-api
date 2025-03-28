<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

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
        User::observe(UserObserver::class);

        //remove if in production to reduce 1ms delay
        if (app()->environment('local')) {
            $expected = env('DB_DATABASE');
            $actual = DB::connection()->getDatabaseName();
            if ($expected !== $actual) {
                logger()->error("DB MISMATCH! Expected: $expected, Actual: $actual");
                abort(500, "Database configuration error");
            }
        }
    }
}
