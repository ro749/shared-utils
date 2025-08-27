<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

class MakeModel extends GeneratorCommand
{
    protected $name = 'make:model'; // Así se llama tu comando Artisan
    protected $description = 'Crea una clase Model personalizada';
    protected $type = 'Model';

    public function handle()
    {
        parent::handle();
        
        if ($this->option('view')) {
            $this->createView();
        }
        if ($this->option('ctrl')) {
            $this->createController();
        }
        if($this->option('all')) {
            $this->createView();
            $this->createController();
        }
    }

    public function create_form_and_table()
    {
        
    }

    protected function getStub()
    {
        return __DIR__ . '/../Stubs/table.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Tables';
    }

    protected function createView()
    {
        $viewName = Str::kebab($this->argument('name'));
        $viewPath = resource_path("views/{$viewName}.blade.php");

        // Si la vista ya existe, avisa y no sobrescribe
        if ($this->files->exists($viewPath)) {
            $this->error("La vista {$viewName} ya existe.");
            return;
        }

        // Obtener el contenido del stub para la vista
        $stub = $this->files->get(__DIR__ . '/../stubs/tableView.stub');

        // Crear el archivo de vista
        $this->files->put($viewPath, $stub);

        $this->info("Vista creada en: {$viewPath}");
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
        $stub = $this->files->get(__DIR__ . '/../stubs/tableController.stub');
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