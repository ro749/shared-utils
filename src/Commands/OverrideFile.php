<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
class OverrideFile extends Command
{
    protected $signature = 'override:file {type} {file}';

    protected $description = 'Copies a file from the template to the project';

    public function handle(): void
    {
        $type = $this->argument('type');
        $file = $this->argument('file');
        $view_override = config('overrides.'.$type.'.'.$file);

        $data = explode('::', $view_override);
        $package = $data[0];
        $file_name = $data[1];

        $content = File::get(base_path('../'.$package.'/resources/views/'.$view.'.blade.php'));
        File::put(base_path('resources/views/'.$view.'.blade.php'), $content);
        $this->call('generate:overrides');
    }
}