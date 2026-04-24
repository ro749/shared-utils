<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\Command;
use Ro749\SharedUtils\Readers\DbReader;
use Ro749\SharedUtils\Readers\DbUpdate;
class ReadCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'read:csv {--file} {--model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->option('file');
        if(empty($file)){
            $file = $this->ask('Enter the path to the CSV file');
        }
        $model = $this->option('model');
        if(empty($model)){
            $model = $this->ask('Enter the model class or table to import to');
        }
        $reader = new DbReader(
            table: $model,
            add_new_columns: true,

        );
        
        $reader->read_cvs($file);
        $this->call('migrate', [
            '--force' => true
        ]);
    }
}