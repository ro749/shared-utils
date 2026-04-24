<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use function Laravel\Prompts\search;
use function Laravel\Prompts\select;
class OverrideView extends Command
{
    protected $signature = 'override:view {--view} {--copy}';

    protected $description = 'Copies the view from the template to the project';

    public function handle(): void
    {
        $view = $this->option('view');
        if(!$view) {
            $views = config('overrides.views');
            $choice = windows_os()
            ? select(
                "Choose a view to override",
                $views,
                scroll: 15,
            )
            : search(
                label: "Choose a view to override",
                placeholder: 'Search...',
                options: fn ($search) => array_values(array_filter(
                    $views,
                    fn ($choice) => str_contains(strtolower($choice), strtolower($search))
                )),
                scroll: 15,
            );
            $view = $choice;
        }
        $this->info('View: '.$view);    
        $copy = $this->option('copy');
        $view_override = config('overrides.views.'.$view);

        $data =explode('::', $view_override);
        
        $package = $data[0];
        $view = $data[1];
        if($copy) {
            $content = File::get(base_path('../'.$package.'/resources/views/'.$view.'.blade.php'));
            File::put(base_path('resources/views/'.$view.'.blade.php'), $content);
        }
        else{
            $original_content = File::get(base_path('../'.$package.'/resources/views/'.$view.'.blade.php'));
            preg_match_all('/\{\{\s*(\$?[a-zA-Z]+)\s*\}\}|\{!!\s*(\$?[a-zA-Z]+)\s*!!\}/', $original_content, $matches);
            $all = array_filter(array_merge($matches[1], $matches[2]));
            $args = [];
            foreach($all as $arg){
                $args[] = '    "'.substr($arg, 1).'" => ""'. PHP_EOL;
            }
            $content = '@include("'.$package.'::'.$view.'", ['.PHP_EOL.implode(',', $args).'])';
            File::put(base_path('resources/views/'.$view.'.blade.php'), $content);
        }
        $this->call('generate:overrides');
    }
}