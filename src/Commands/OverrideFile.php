<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OverrideFile extends Command
{
    protected $signature = 'override:file {type} {file}';

    protected $description = 'Copies a file from the template to the project';

    public function handle(): void
    {
        $type = $this->argument('type');
        $file = $this->argument('file');
        $file_override = config('overrides.'.$type.'s.'.$file);
        $route_parts = explode('\\', $file_override);
        $route_parts[1] = Str::kebab($route_parts[1]);
        $package = $route_parts[1];
        $route = implode('/', array_slice($route_parts, 2));

        $content = File::get(path: base_path('../'.$package.'/src/'.$route.'.php'));
        $content = preg_replace('/namespace Ro749\\\\.*\\\\/', 'namespace App\\', $content);
        $filePath = app_path($route.'.php');
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }
        File::put(app_path($route.'.php'), $content);
        $this->call('generate:overrides');
    }
}