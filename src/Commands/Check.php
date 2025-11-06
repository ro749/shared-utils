<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
class Check extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Makes a check of all the project and helps to fix them.';

    public function handle(): void
    {
        $has_overrides = config()->has('overrides');
        if(!$has_overrides) {
            if($this->confirm('Overrides not found, do you want to generate them?')){
                $this->call('generate:overrides');
                $this->info('generated overrides');
            }
            else{
                return;
            }
        }

        $composer_json = json_decode(file_get_contents(base_path('composer.json')), true);
        $fixed_composer_json = false;
        foreach($composer_json['repositories'] as $key => $package) {
            if ($composer_json['repositories'][$key]['type'] !== 'vcs') {
                $fixed_composer_json = true;
                $composer_json['repositories'][$key]['type'] = 'vcs';
                $composer_json['repositories'][$key]['url'] = 
                str_replace(
                    '..', 
                    'https://github.com', 
                    $composer_json['repositories'][$key]['url']
                ).'.git';
            }
        }
        if($fixed_composer_json) {
            file_put_contents(
                base_path('composer.json'), 
                json_encode($composer_json,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            );
            $this->info('fixed composer.json');
        }

        $vendor_folder = base_path('public\vendor');
        $vendor_dirs = File::directories($vendor_folder);
        foreach($vendor_dirs as $dir) {
            $folder = basename($dir);
            $package = base_path('..\\'.$folder.'\resources\dist');
            $project_files = File::allFiles($dir);
            foreach($project_files as $file) {
                $relative_path = $file->getRelativePathname();
                $file_in_package = $package.'/'.$relative_path;
                if(!File::exists($file_in_package)) {
                    File::copy($file, $file_in_package);
                    $this->info('Copied '.$relative_path.' to package.');
                }
                else if (File::hash($file) !== File::hash($file_in_package)){
                    File::copy($file, $file_in_package);
                    $this->info('Updated '.$relative_path.' to package.');
                }
            }
        }

        $this->info('✓ Everything fixed!');
    }

}