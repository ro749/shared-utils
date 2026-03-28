<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use function Laravel\Prompts\search;
use function Laravel\Prompts\select;
use function PHPSTORM_META\map;

class OverrideFile extends Command
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
        //$this->info("Package: $package");
        $route = implode('/', array_slice($route_parts, 2));

        //$this->info(base_path('../'.$package.'/src/'.$route.'.php'));
        $baseContent = File::get(path: base_path('../'.$package.'/src/'.$route.'.php'));

        
        $matches = [];
        preg_match('/namespace Ro749\\\\.*\\\\(\w+)/', $baseContent, $matches);
        $namespace_name = $matches[1];
        //$this->info("Namespace $namespace_name");

        $matches = [];
        preg_match('/class\s+(\w+)\s+extends\s+(\w+)/', $baseContent, $matches);
        $class_name = $matches[1];
        //$this->info("class $class_name");
        $parent_class = $matches[2];
        //$this->info("parent class $parent_class");

        /*$matches = [];
        preg_match('/use (.*\\\\)*(.*)\\\\/', $baseContent, $matches);
        $type_name = "";
        if (empty($matches)) {
            $this->info("Couldn't find type name.");
        }
        else{
            $type_name = $matches[2];
            $this->info("type $type_name");
        }*/

        $matches = [];
        preg_match('/public function __construct()/', $baseContent, $matches);
        $has_contructor = !empty($matches);


        $content = "<?php\n\n";
        $content = $content."namespace App\\$namespace_name;\n\n";
        $content = $content."use Ro749\\$packageNK\\$type"."s\\$class_name as $parent_class;\n\n";
        $content = $content."class $class_name extends $parent_class\n";
        $content = $content."{\n";
        if ($has_contructor) {
            $content = $content."\tpublic function __construct() {\n";
            $content = $content."\t\tparent::__construct();\n";
            $content = $content."\t}\n";
        }
        $content = $content."}";

        /*$content = preg_replace('/namespace Ro749\\\\.*\\\\/', 'namespace App\\', $content);

        $matches = [];
        preg_match('/class\s+(\w+)\s+extends\s+(\w+)/', $content, $matches);
        $parent_class = $matches[2];
        $content = preg_replace('/use (.*\\\\)*(.*)\\\\'.$parent_class.';/', 'use Ro749\\\\'.$packageNK.'\\\\$2\\\\'.$file.' as '.$parent_class.';', $content);
        $content = preg_replace('/parent::__construct\((((.*)\s)*)\);/', 'parent::__construct();', $content);*/

        $filePath = app_path($route.'.php');
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }
        File::put(app_path($route.'.php'), $content);

        $this->call('generate:overrides');
    }
}