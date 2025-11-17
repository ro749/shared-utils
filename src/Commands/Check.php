<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\select;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use Illuminate\Support\Facades\Process;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
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

    function generate_overrides(){
        $this->call('generate:overrides');
    }

    function fix_composer_json(){
        $composer_json = json_decode(file_get_contents(base_path('composer.json')), true);
        $fixed_composer_json = false;
        foreach($composer_json['repositories'] as $key => $package) {
            if ($composer_json['repositories'][$key]['type'] !== 'vcs') {
                $fixed_composer_json = true;
                $composer_json['repositories'][$key]['type'] = 'vcs';
                $composer_json['repositories'][$key]['url'] = 
                str_replace(
                    '..', 
                    'git@github.com:ro749', 
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
    }

    function fix_gitignore(){
        $gitignore_path = base_path('.gitignore');
        $gitignore_content = file_get_contents($gitignore_path);
        if (strpos($gitignore_content, 'composer.lock') === false) {
            file_put_contents(
                $gitignore_path, 
                $gitignore_content.PHP_EOL . 'composer.lock'
            );
            Process::run('git rm --cached composer.lock');
            $this->info('Added composer.lock to .gitignore');
        }
    }

    function model_fix(){
        
        $models = config('overrides.models');
        $model_tables = [];
        $parser_factory = new ParserFactory();
        $parser = $parser_factory->createForHostVersion();
        $printer = new PrettyPrinter\Standard();
        $this->info(json_encode($models, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        foreach($models as $model) {
            
            $model = new $model;
            if(Schema::hasTable($model->getTable())) {
                continue;
            }
            if($this->confirm('Create table for '.$model->getTable().'?')) {
                $name = $this->ask('Name for the table: (Empty for auto generate)');
                $this->call('make:migration', [
                    'name' => 'create_models_tables',
                    '--create' => empty($name)?$model->getTable():$name
                ]);
                if(!empty($name)){
                    $model_tables[$model::class] = $name;
                }
            }
            else{
                if($this->confirm('Assign table for '.$model->getTable().'?')){
                    if(empty($tables)){
                        $tables = DB::select('SHOW TABLES');
                        $db = "Tables_in_".env('DB_DATABASE');
                        $tables = array_column($tables, $db);
                        $tables = array_diff($tables, [
                            'cache',
                            'cache_locks',
                            'failed_jobs',
                            'job_batches',
                            'jobs',
                            'migrations',
                            'password_reset_tokens',
                            'sessions',
                            'settings'
                        ]);
                    }
                    $table_id = select('Choose table to assign it to', $tables);
                    $model_tables[$model::class] = $tables[$table_id];
                }
            }
        }
        foreach($model_tables as $model => $table) {
            $code = file_get_contents(base_path($model).'.php');
            $ast = $parser->parse($code);
            $traverser = new NodeTraverser();
            $traverser->addVisitor(new class($table) extends NodeVisitorAbstract {
                private $table;
                public function __construct($table) {
                    $this->table = $table;
                }
                public function leaveNode(Node $node) {
                    // Aquí modificamos los nodos
                    if ($node instanceof Node\Stmt\Class_) {
                        $propiedad = new Node\Stmt\Property(
                            Node\Stmt\Class_::MODIFIER_PROTECTED,
                            [
                                new Node\Stmt\PropertyProperty(
                                    'table',
                                    new Node\Scalar\String_($this->table)
                                )
                            ]
                        );

                        // Agregarla al inicio de la clase
                        array_unshift($node->stmts, $propiedad);
                    }
                }
            });
            $ast = $traverser->traverse($ast);
            $new_code = $printer->prettyPrintFile($ast);
            file_put_contents(base_path($model).'.php', $new_code);
        }
        
    }

    public function handle(): void
    {
        $this->generate_overrides();
        $this->fix_composer_json();
        $this->fix_gitignore();
        $this->model_fix();

        $this->info('✓ Everything fixed!');
    }

}