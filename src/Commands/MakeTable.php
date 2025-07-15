<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeTable extends GeneratorCommand
{
    protected $name = 'make:table'; // Así se llama tu comando Artisan
    protected $description = 'Crea una clase Table personalizada';
    protected $type = 'Table';

    protected function getStub()
    {
        return __DIR__ . '/../Stubs/table.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Tables';
    }
}