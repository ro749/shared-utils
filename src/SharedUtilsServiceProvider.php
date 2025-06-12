<?php

namespace Ro749\SharedUtils;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Ro749\SharedUtils\Commands\SharedUtilsCommand;
use Illuminate\Support\Facades\Blade;

class SharedUtilsServiceProvider extends PackageServiceProvider
{
    

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('shared-utils')
            ->hasConfigFile()
            ->hasAssets(['resources/css/base.css'])
            ->hasViews()
            ->hasMigration('create_shared_utils_table')
            ->hasCommand(SharedUtilsCommand::class);
    }

    public function packageBooted(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'sharedutils');
        $this->loadViewsFrom(__DIR__ . '/../resources/views/', 'sharedutils');
        // Register your component: <x-sharedutils::modal />
        Blade::component('sharedutils::components.modal', 'sharedutils::modal');
        //Blade::component('sharedutils::components.inputs.select', 'sharedutils::select');
        //Blade::component('sharedutils::components.inputs.db-select', 'sharedutils::db-select');
    }
}
