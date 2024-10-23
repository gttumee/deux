<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Parallax\FilamentComments\Models\FilamentComment;
use App\Observers\CommentObserver;

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
        FilamentComment::observe(CommentObserver::class);
    }
}