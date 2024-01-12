<?php

namespace GrantHolle\Altcha;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AltchaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-altcha')
            ->hasConfigFile();
    }

    public function packageRegistered()
    {
        $this->app->bind(Altcha::class, fn () => new Altcha(
            config('altcha.algorithm'),
            config('altcha.hmac_key'),
            config('altcha.range_min'),
            config('altcha.range_max'),
        ));
    }
}
