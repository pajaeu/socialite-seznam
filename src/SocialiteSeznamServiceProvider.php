<?php

declare(strict_types=1);

namespace Pajaeu\SocialiteSeznam;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;

final class SocialiteSeznamServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $socialite = $this->app->make(Factory::class);

        $socialite->extend('seznam', function () use ($socialite) {
            $config = config('services.seznam');

            return $socialite->buildProvider(SocialiteSeznamProvider::class, $config);
        });
    }
}
