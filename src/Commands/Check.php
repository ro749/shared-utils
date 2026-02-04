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

    public array $default_tables = [
        'cache',
        'cache_locks',
        'failed_jobs',
        'job_batches',
        'jobs',
        'migrations',
        'password_reset_tokens',
        'sessions',
        'settings'
    ];

    public array $default_columns = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public array $model_tables = [];

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
            $gitignore_content = $gitignore_content.PHP_EOL . 'composer.lock';
            file_put_contents(
                $gitignore_path, 
                $gitignore_content
            );
            Process::run('git rm --cached composer.lock');
            $this->info('Added composer.lock to .gitignore');
        }
        if (strpos($gitignore_content, '/public/vendor/') === false) {
            $gitignore_content = $gitignore_content.PHP_EOL . '/public/vendor/';
            file_put_contents(
                $gitignore_path, 
                $gitignore_content
            );
            Process::run('git rm -r --cached public/vendor');
            $this->info('Added public/vendor to .gitignore');
        }
    }

    function check_model($model){
        $model = new $model;
        if(Schema::hasTable($model->getTable())) {
            return;
        }
        if($this->confirm('Create table for '.$model->getTable().'?')) {
            $name = $this->ask('Name for the table: (Empty for auto generate)');
            $this->call('make:migration', [
                'name' => 'create_models_tables',
                '--create' => empty($name)?$model->getTable():$name
            ]);
            if(!empty($name)){
                $this->model_tables[$model::class] = $name;
            }
        }
        else{
            if($this->confirm('Assign table for '.$model->getTable().'?')){
                if(empty($tables)){
                    $tables = DB::select('SHOW TABLES');
                    $db = "Tables_in_".env('DB_DATABASE');
                    $tables = array_column($tables, $db);
                    $tables = array_diff($tables, $this->default_tables);
                }
                $table_id = select('Choose table to assign it to', $tables);
                $model_tables[$model::class] = $tables[$table_id];
            }
        }

        
        
    }
    function fix_model($model, $table){
        $parser_factory = new ParserFactory();
        $parser = $parser_factory->createForHostVersion();
        $printer = new PrettyPrinter\Standard();
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

    function check_fillable($model){
        $table_columns = Schema::getColumnListing($model->getTable());
        $table_columns = array_diff($table_columns,$this->default_columns);
        
        $fillable_columns = $model->getFillable();
        
        $unfillable_columns = array_diff($table_columns,$fillable_columns);
        if(count($unfillable_columns) > 0){
            $this->info('The following columns of '.$model->getTable().' are not fillable: '.implode(', ',$unfillable_columns));
            $this->info('Making them fillable...');
            $parser_factory = new ParserFactory();
            $parser = $parser_factory->createForHostVersion();
            $printer = new PrettyPrinter\Standard();
            $code = file_get_contents(base_path($model::class).'.php');
            $ast = $parser->parse($code);
            $traverser = new NodeTraverser();
            $traverser->addVisitor(new class($table_columns) extends NodeVisitorAbstract {
                private array $table_columns;
                public bool $fillableFound = false;
                public function __construct($table_columns) {
                    $this->table_columns = $table_columns;
                }
                public function leaveNode(Node $node) {
                    // Aquí modificamos los nodos
                    if ($node instanceof Node\Stmt\Class_) {
                        foreach ($node->stmts as $key => $stmt) {
                            if ($stmt instanceof Node\Stmt\Property 
                                && $stmt->props[0]->name->toString() === 'fillable') {
                                // REEMPLAZAR el fillable existente
                                $stmt->props[0]->default = new Node\Expr\Array_(
                                    array_map(
                                        fn($col) => new Node\ArrayItem(new Node\Scalar\String_($col)),
                                        $this->table_columns
                                    )
                                );
                                $this->fillableFound = true;
                                return null;
                            }
                        }

                        // Si no existe, CREAR la propiedad $fillable
                        if (!$this->fillableFound) {
                            $fillableProperty = new Node\Stmt\Property(
                                Node\Stmt\Class_::MODIFIER_PROTECTED,
                                [
                                    new Node\Stmt\PropertyProperty(
                                        'fillable',
                                        new Node\Expr\Array_(
                                            array_map(
                                                fn($col) => new Node\ArrayItem(new Node\Scalar\String_($col)),
                                                $this->table_columns
                                            )
                                        )
                                    )
                                ]
                            );

                            // Insertar después del "use HasFactory;"
                            array_push($node->stmts, $fillableProperty);
                        }
                    }
                }
            });
            $ast = $traverser->traverse($ast);
            $new_code = $printer->prettyPrintFile($ast);
            file_put_contents(base_path($model::class).'.php', $new_code);
        } 
    }

    function model_fix(){
        
        
        $models = config('overrides.models');
        
        $this->info(json_encode($models, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        foreach($models as $model) {
            $this->check_model($model);
        }
        foreach($this->model_tables as $model => $table) {
            $this->fix_model($model,$table);
        }
        $this->call('migrate');
        foreach($models as $model) {
            $this->check_fillable(new $model);
        }
        
    }

    public function handle(): void
    {
        $this->generate_overrides();
        $this->fix_composer_json();
        $this->fix_gitignore();
        //$this->model_fix();
        

        $this->info('✓ Everything fixed!');
    }

}