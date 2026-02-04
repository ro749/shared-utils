<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
class OverrideView extends Command
{
    protected $signature = 'override:view {view}';

    protected $description = 'Copies the view from the template to the project';

    public function handle(): void
    {
        $view = $this->argument('view');
        $view_override = config('overrides.views.'.$view);

        $data =explode('::', $view_override);
        $package = $data[0];
        $view = $data[1];

        $content = File::get(base_path('../'.$package.'/resources/views/'.$view.'.blade.php'));
        File::put(base_path('resources/views/'.$view.'.blade.php'), $content);
        $this->call('generate:overrides');
    }
}