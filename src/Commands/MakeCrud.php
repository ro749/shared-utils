<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

class MakeCrud extends GeneratorCommand
{
    protected $name = 'make:crud'; // Así se llama tu comando Artisan
    protected $description = 'Crea una crud (Registro y Tabla) para un modelo';
    protected $type = 'Model';

    public function handle()
    {
        parent::handle();
        $this->create_form_and_table();
        if ($this->option('view')) {
            $this->createViews();
        }
        if ($this->option('ctrl')) {
            $this->createController();
        }
        if($this->option('all')) {
            $this->createViews();
            $this->createController();
        }
    }

    public function create_form_and_table()
    {
        $form_name = Str::pascal($this->argument('name'));
        $table_name = $form_name.'s';
        $formPath = app_path("Http/Requests/Register{$form_name}.php");
        $tablePath = app_path("Tables/{$table_name}Table.php");
        $formStub = $this->files->get(__DIR__ . '/../Stubs/formRegisterModel.stub');
        $tableStub = $this->files->get(__DIR__ . '/../Stubs/tableModel.stub');
        $formStub = str_replace(
            '{{ class }}',
            $form_name,
            $formStub
        );
        $tableStub = str_replace(
            '{{ class }}',
            $table_name,
            $tableStub
        );
        $this->files->put($formPath, $formStub);
        $this->files->put($tablePath, $tableStub);
        $this->info("Formulario y tabla creados en: {$formPath} y {$tablePath}");

    }

    protected function getStub()
    {
        return __DIR__ . '/../Stubs/model.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Models';
    }

    protected function createViews()
    {
        $viewFormName = 'register-'.Str::kebab($this->argument('name'));
        $viewTableName = 'table-'.Str::kebab($this->argument('name'));
        $viewFormPath = resource_path("views/{$viewFormName}.blade.php");
        $viewTablePath = resource_path("views/{$viewTableName}.blade.php");
        // Obtener el contenido del stub para la vista
        $form_stub = $this->files->get(__DIR__ . '/../stubs/formView.stub');
        $table_stub = $this->files->get(__DIR__ . '/../stubs/tableView.stub');

        // Crear el archivo de vista
        $this->files->put($viewFormPath, $form_stub);
        $this->files->put($viewTablePath, $table_stub);

        $this->info("Vistas creadas en: {$viewFormPath} y {$viewTablePath}");
    }

    protected function createController()
    {
        $controllerName = Str::pascal($this->argument('name'));
        $viewName = Str::kebab($this->argument('name'));
        $controllerPath = app_path("Http/Controllers/{$controllerName}Controller.php");

        // Si el controlador ya existe, avisa y no sobrescribe
        if ($this->files->exists($controllerPath)) {
            $this->error("El controlador {$controllerName} ya existe.");
            return;
        }

        // Obtener el contenido del stub para el controlador
        $stub = $this->files->get(__DIR__ . '/../stubs/modelController.stub');
        $stub = str_replace(
            ['{{ class }}', '{{ view }}'],
            [$controllerName, $viewName],
            $stub
        );
        // Crear el archivo de controlador
        $this->files->put($controllerPath, $stub);

        $this->info("Controlador creado en: {$controllerPath}");
        
    }

    protected function getOptions()
    {
        return [
            ['view', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Generar una vista adicional'],
            ['ctrl', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Generar un controlador adicional'],
            ['all', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Generar todo'],
        ];
    }
}