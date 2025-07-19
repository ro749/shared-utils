<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
class Options extends Command
{
    protected $signature = 'generate:option';
    protected $description = 'Genera un archivo JS con las opciones globales desde config/options.php';

    protected function generateEnum(array $keys): void
    {
        $enumPath = app_path('Enums/Options.php');
    
        $lines = [
            "<?php",
            "",
            "namespace App\Enums;",
            "",
            "enum Options: string",
            "{"
        ];
    
        foreach ($keys as $key) {
            $caseName = Str::studly($key); // statusOptions → StatusOptions
            $lines[] = "    case {$caseName} = '{$key}';";
        }
    
        $lines[] = "}";
    
        File::ensureDirectoryExists(app_path('Enums'));
        File::put($enumPath, implode("\n", $lines));
    
        $this->info("Enum generado: App\\Enums\\OptionKey");
    }

    public function handle()
    {
        $options = config('options');

        $jsContent = '';

        foreach ($options as $varName => $optionArray) {
            $jsContent .= "var {$varName} = " . json_encode($optionArray, JSON_UNESCAPED_UNICODE) . ";\n";
        }

        File::put(public_path('js/options.js'), $jsContent);

        $this->info('Archivo JS generado: public/js/options.js');

        $this->generateEnum(array_keys($options));
    }
}