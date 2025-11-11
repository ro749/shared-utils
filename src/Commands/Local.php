<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
class Local extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'local';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepares the project for working local, use check before uploading to production.';

    function fix_composer_json(){
        $composer_json = json_decode(file_get_contents(base_path('composer.json')), true);
        $fixed_composer_json = false;
        foreach($composer_json['repositories'] as $key => $package) {
            if ($composer_json['repositories'][$key]['type'] !== 'path') {
                $fixed_composer_json = true;
                $composer_json['repositories'][$key]['type'] = 'path';
                $composer_json['repositories'][$key]['url'] = 
                str_replace(
                    'git@github.com:ro749', 
                    '..', 
                    $composer_json['repositories'][$key]['url']
                );
                $composer_json['repositories'][$key]['url'] = 
                str_replace(
                    '.git', 
                    '', 
                    $composer_json['repositories'][$key]['url']
                );
            }
        }
        if($fixed_composer_json) {
            file_put_contents(
                base_path('composer.json'), 
                json_encode($composer_json,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            );
            $this->info('made local composer.json');
        }
    }

    public function handle(): void
    {
        $this->call('reimport');
        $this->fix_composer_json();
        $this->info('✓ Everything local!');
    }

}