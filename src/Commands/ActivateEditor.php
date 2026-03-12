<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Process;
class ActivateEditor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'editor:activate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activates the editor for this project.';

    public function handle(): void
    {
        $composer_json = json_decode(file_get_contents(base_path('composer.json')), true);
        if (!array_key_exists('ro749/listing-editor', $composer_json['require'])) {
            $composer_json['require']['ro749/listing-editor'] = 'dev-main';
            if($composer_json['repositories'][0]['type'] == 'vcs') {
                $composer_json['repositories'][] = [
                    'type' => 'vcs',
                    'url' => 'git@github.com:ro749/listing-editor.git'
                ];
            }
            else{
                $composer_json['repositories'][] = [
                    'type' => 'path',
                    'url' => '../listing-editor'
                ];
            }
        }
        file_put_contents(base_path('composer.json'), json_encode($composer_json,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        Process::run('composer update');
    }

}