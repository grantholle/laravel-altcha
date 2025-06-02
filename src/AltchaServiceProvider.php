<?php

namespace GrantHolle\Altcha;

use GrantHolle\Altcha\Http\Controllers\AltchaChallengeController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AltchaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('laravel-altcha')->hasConfigFile();
    }

    public function packageRegistered()
    {
        $this->app->bind(\AltchaOrg\Altcha\Altcha::class, fn () => new \AltchaOrg\Altcha\Altcha(
            config('altcha.hmac_key')
        ));

        $this->app->bind(Altcha::class, fn (Application $app) => new Altcha(
            $app->make(\AltchaOrg\Altcha\Altcha::class),
            $app['config']->get('altcha.algorithm'),
            $app['config']->get('altcha.range_max')
        ));
    }

    public function packageBooted()
    {
        if ($path = config('altcha.route')) {
            Route::get($path, AltchaChallengeController::class)
                ->name('altcha-challenge')
                ->middleware(config('altcha.middleware'));
        }
    }
}
