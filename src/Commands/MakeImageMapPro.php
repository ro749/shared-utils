<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

class MakeImageMapPro extends GeneratorCommand
{
    protected $name = 'make:imp'; // Así se llama tu comando Artisan
    protected $description = 'Crea una clase Image Map Pro';
    protected $type = 'ImageMapPro';

    protected function getStub()
    {
        return __DIR__ . '/../Stubs/imageMapPro.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\ImageMapPro';
    }
}