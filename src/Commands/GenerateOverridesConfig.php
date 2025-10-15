<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
class GenerateOverridesConfig extends Command
{
    protected $signature = 'config:generate-overrides';
    protected $description = 'Genera el archivo config/overrides.php con todas las clases';

    public function handle()
    {
        $overrides = [];

        // Procesar Tables y Forms (todos los archivos)
        $overrides['tables'] = $this->getClassesFromFolder('app/Tables');
        $overrides['forms'] = $this->getClassesFromFolder('app/Forms');
        $overrides['models'] = $this->getClassesFromFolder('app/Models');
        $overrides['controllers'] = $this->getClassesFromFolder('app/Http/Controllers');
        // Procesar Plans e ImageMapPro (solo archivos únicos, no carpetas)
        $plansOverrides = $this->getClassesFromFolder('app/Plans');
        if (!empty($plansOverrides)) {
            $overrides['plans'] = $plansOverrides;
        }
        $imageMapProOverrides = $this->getClassesFromFolder('app/ImageMapPro');
        if (!empty($imageMapProOverrides)) {
            $overrides['image_map_pro'] = $imageMapProOverrides;
        }
        // Generar contenido del archivo
        $content = $this->generatePhpContent($overrides);

        // Crear directorio si no existe
        if (!File::exists(config_path(''))) {
            File::makeDirectory(config_path(''), 0755, true);
        }

        // Escribir archivo
        File::put(config_path('overrides.php'), $content);

        $this->info('✓ Archivo config/overrides.php generado exitosamente');
        $this->info('Total de clases registradas: ' . count($overrides));
    }

    private function getClassesFromFolder($folder)
    {
        $classes = [];
        $appPath = base_path($folder);

        if (!File::isDirectory($appPath)) {
            return $classes;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($appPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php') {
                $className = $this->getClassNameFromFile($file);
                if ($className) {
                    $classes[$className['shortName']] = $className['fullName'];
                }
            }
        }

        return $classes;
    }

    private function getClassNameFromFile(SplFileInfo $file)
    {
        $content = File::get($file->getRealPath());
        
        // Extraer namespace
        if (preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatch)) {
            $namespace = trim($namespaceMatch[1]);
        } else {
            return null;
        }

        // Extraer nombre de clase
        if (preg_match('/class\s+(\w+)(\s|{)/', $content, $classMatch)) {
            $className = $classMatch[1];
            $fullName = $namespace . '\\' . $className;

            return [
                'shortName' => $className,
                'fullName' => $fullName . '::class',
            ];
        }

        return null;
    }

    private function generatePhpContent($overrides)
    {
        $content = "<?php\n\n";
        $content .= "return [\n";

        foreach ($overrides as $key_override => $values) {
            
            if(($key_override == "plans" || $key_override == "image_map_pro") && count($values) == 1){
                $content .= "    '$key_override' => " . array_values($values)[0] . ",\n";
            }
            else{
                $content .= "    '{$key_override}' => [\n";
                foreach($values as $key => $value) {
                    $content .= "        '{$key}' => {$value},\n";
                }
                $content .= "    ],\n";
            }
            
            
        }

        $content .= "];\n";

        return $content;
    }
}