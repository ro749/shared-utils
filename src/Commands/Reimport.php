<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Reimport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reimport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reimporta public/vendor';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $vendor_folder = base_path('public\vendor');
        $vendor_dirs = File::directories($vendor_folder);
        foreach($vendor_dirs as $dir) {
            $package_name = basename($dir);
            File::deleteDirectory($dir);
            $this->call('php artisan vendor:publish --tag='.$package_name.'-assets --force');
        }
        return 0;
    }
}
