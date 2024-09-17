<?php

namespace OmniTerm;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class OmniTermServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('omniterm')
            ->hasViews('omniterm');
    }
}
