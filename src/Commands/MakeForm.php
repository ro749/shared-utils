<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeForm extends GeneratorCommand
{
    protected $name = 'make:form'; // Así se llama tu comando Artisan
    protected $description = 'Crea una form';
    protected $type = 'Form';

    protected function getStub()
    {
        return __DIR__ . '/../Stubs/form.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Requests';
    }
}