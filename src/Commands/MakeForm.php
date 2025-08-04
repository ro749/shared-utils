<?php

namespace Ro749\SharedUtils\Commands;
use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

class MakeForm extends GeneratorCommand
{
    protected $name = 'make:form'; // Así se llama tu comando Artisan
    protected $description = 'Crea una form';
    protected $type = 'Form';

    public function handle()
    {
        // Genera el controlador normalmente
        parent::handle();

        // Si el usuario pasa una opción para crear la vista
        if ($this->option('view')) {
            $this->createView();
        }
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
        $stub = $this->files->get(__DIR__ . '/../stubs/formView.stub');

        // Crear el archivo de vista
        $this->files->put($viewPath, $stub);

        $this->info("Vista creada en: {$viewPath}");
    }

    protected function getStub()
    {
        return __DIR__ . '/../Stubs/form.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Requests';
    }

    protected function getOptions()
    {
        return [
            ['view', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Generar una vista adicional'],
        ];
    }

}