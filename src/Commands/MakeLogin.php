<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeLogin extends GeneratorCommand
{
    protected $name = 'make:login'; // Así se llama tu comando Artisan
    protected $description = 'Crea un login';
    protected $type = 'Login';

    protected function getStub()
    {
        return __DIR__ . '/../Stubs/login.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Requests';
    }
}