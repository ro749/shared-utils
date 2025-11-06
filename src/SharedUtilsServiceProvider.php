<?php

namespace Ro749\SharedUtils;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Ro749\SharedUtils\Commands\SharedUtilsCommand;
use Ro749\SharedUtils\Commands\MakeTable;
use Ro749\SharedUtils\Commands\MakeLogin;
use Ro749\SharedUtils\Commands\MakeForm;
use Ro749\SharedUtils\Commands\Options;
use Ro749\SharedUtils\Commands\MakeCrud;
use Ro749\SharedUtils\Commands\ReadCsv;
use Ro749\SharedUtils\Commands\GenerateOverridesConfig;
use Ro749\SharedUtils\Commands\Check;
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
            ->hasAssets()
            ->hasViews()
            ->hasMigration('create_shared_utils_table')
            ->hasCommands([
                SharedUtilsCommand::class,
                MakeTable::class,
                MakeLogin::class,
                MakeForm::class,
                Options::class,
                MakeCrud::class,
                ReadCsv::class,
                Check::class,
                GenerateOverridesConfig::class
            ])
            ->hasRoutes('web');
    }

    public function packageBooted(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'sharedutils');
        // Register your component: <x-sharedutils::modal />
        Blade::component('sharedutils::components.modal', 'sharedutils::modal');
        Blade::component('sharedutils::components.forms.selector', 'sharedutils::selector');
        Blade::component('sharedutils::components.forms.base-field', 'field');
        Blade::component('sharedutils::components.fillables.fillable-text', 'f-text');
        Blade::component('sharedutils::components.fillables.fillable-image', 'f-image');
        Blade::component('sharedutils::components.fillables.fillable-conditional', 'f-conditional');
        Blade::component('sharedutils::components.ajax-form', 'smartForm');
        Blade::component('sharedutils::components.tables.smartTable', 'smartTable');
        Blade::component('sharedutils::components.tables.localSmartTable', 'localSmartTable');
        Blade::component('sharedutils::components.tables.layeredSmartTable', 'layeredSmartTable');
    }
}
