<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
class FixView extends Command
{
    protected $signature = 'fix:view {view}';

    protected $description = 'Fixes a view generated outside to match standards';

    public function handle(): void
    {
        $view = $this->argument('view');
        $content = File::get(base_path('resources\views\\'.$view.'.blade.php'));
        $content = preg_replace('/src="((?:(?!\{\{).)*?)"/', 'src="{{ image(\'$1\') }}"', $content);
        File::put(base_path('resources\views\\'.$view.'.blade.php'), $content);
    }
}