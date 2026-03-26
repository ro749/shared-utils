<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use function Laravel\Prompts\search;
use function Laravel\Prompts\select;

class OverrideFile extends Command implements PromptsForMissingInput
{
    protected $signature = 'override:file {type?} {file?}';

    protected $description = 'Copies a file from the template to the project';

    public function handle(): void
    {
        $type = $this->argument('type');
        $file = $this->argument('file');

        $allConfigs = config('overrides');
        unset($allConfigs["views"]);
        unset($allConfigs["image_map_pro"]);
        unset($allConfigs["sender"]);
        $configTypes = array_keys($allConfigs);

        if (!isset($allConfigs[$type]) && isset($allConfigs[$type."s"])) {
            $type = $type."s";
        }

        if (!$type || !isset($allConfigs[$type])) {
            $this->info("Type missing or non-existent.");

            $choice = windows_os()
            ? select(
                "Choose a valid type to override",
                $configTypes,
                scroll: 15,
            )
            : search(
                label: "Choose a valid type to override",
                placeholder: 'Search...',
                options: fn ($search) => array_values(array_filter(
                    $configTypes,
                    fn ($choice) => str_contains(strtolower($choice), strtolower($search))
                )),
                scroll: 15,
            );

            $type = $choice;
        }

        if (!$file || !isset($allConfigs[$type][$file])) {
            $this->info("File missing or non-existent.");

            $configFiles = array_keys($allConfigs[$type]);

            $choice = windows_os()
            ? select(
                "Choose a valid file to override",
                $configFiles,
                scroll: 15,
            )
            : search(
                label: "Choose a file type to override",
                placeholder: 'Search...',
                options: fn ($search) => array_values(array_filter(
                    $configFiles,
                    fn ($choice) => str_contains(strtolower($choice), strtolower($search))
                )),
                scroll: 15,
            );

            $file = $choice;
        }
        
        $this->info("Overriding $type -> $file");

        if (str_ends_with($type, 's')) {
            $type = substr($type, 0, -1);
        }

        $file_override = config('overrides.'.$type.'s.'.$file);
        $route_parts = explode('\\', $file_override);
        //$this->info("route parts: ".json_encode($route_parts, JSON_PRETTY_PRINT));

        $packageNK = $route_parts[1];
        $route_parts[1] = Str::kebab($route_parts[1]);
        $package = $route_parts[1];
        $route = implode('/', array_slice($route_parts, 2));

        $content = File::get(path: base_path('../'.$package.'/src/'.$route.'.php'));
        $content = preg_replace('/namespace Ro749\\\\.*\\\\/', 'namespace App\\', $content);

        $matches = [];
        preg_match('/class\s+(\w+)\s+extends\s+(\w+)/', $content, $matches);
        $parent_class = $matches[2];
        $content = preg_replace('/use (.*\\\\)*(.*)\\\\'.$parent_class.';/', 'use Ro749\\\\'.$packageNK.'\\\\$2\\\\'.$file.' as '.$parent_class.';', $content);
        $content = preg_replace('/parent::__construct\((((.*)\s)*)\);/', 'parent::__construct();', $content);

        $filePath = app_path($route.'.php');
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }
        File::put(app_path($route.'.php'), $content);
        $this->call('generate:overrides');
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'type' => ['What type of data are you overiding?', "E.g. Table"],
        ];
    }
}