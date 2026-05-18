<?php

namespace Ro749\SharedUtils\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class InitProject extends Command
{
    protected $signature = 'init:project
    {--db_name=}
    {--ext-img-dir=}

    {--none}
    {--ci}

    {--skip-env}
    {--skip-db-create}
    {--skip-migrations}
    {--skip-seed}
    {--skip-publish}
    {--skip-external-disk}

    {--create-env}
    {--create-db}
    {--do-migrations}
    {--do-seeding}
    {--publish-assets}
    {--create-external-disk}
    ';

    protected $description = 'Initializes the project by creating the .env file, creating a default admin user and publishing the necessary assets.';

    public function handle(): void
    {
        $none = $this->option('none');
        $isCi = $this->option('ci');

        $skipEnv = $none ? !$this->option('create-env') : $this->option('skip-env') || $isCi;
        $skipDbCreate = $none ? !$this->option('create-db') : $this->option('skip-db-create') || $isCi;
        $skipMigrations = $none ? !$this->option('do-migrations') : $this->option('skip-migrations') || $isCi;
        $skipSeed = $none ? !$this->option('do-seeding') : $this->option('skip-seed');
        $skipPublish = $none ? !$this->option('publish-assets') : $this->option('skip-publish');
        $skipExternalDisk = $none ? !$this->option('create-external-disk') : $this->option('skip-external-disk');

        $dbName = $this->option('db_name');
        if (! $isCi && (! $skipEnv || ! $skipDbCreate) && ($dbName === null || $dbName === '')) {
            $dbName = $this->ask('What is the name of your database?');
        }

        if (! $skipDbCreate && ($dbName === null || $dbName === '' || ! preg_match('/^[A-Za-z0-9_]+$/', $dbName))) {
            $this->error('Invalid database name. If you don\'t want to create a database name, use the --skip-db-create and --skip-env options.');
            return;
        }

        if ($skipEnv) {
            $this->info('Skipping .env file creation.');
        } else {
            $this->createEnvFile($dbName);
        }

        if ($skipDbCreate) {
            $this->info('Skipping db creation.');

            if ($skipMigrations) {
                $this->info('Skipping migration.');
            } else {
                $this->call('migrate', ['--force' => true]);
                $this->info('Migration completed.');
            }
        } else {
            $this->createDatabase($dbName, $skipMigrations);
        }

        $this->call('generate:overrides');

        if ($skipSeed) {
            $this->info('Skipping seeding.');
        } else {
            $this->createDefaultUsers();
            $this->createDefaultQuotation();
        }

        if ($skipPublish) {
            $this->info('Skipping publish.');
        } else {
            $this->publishAssets();
        }

        if ($skipExternalDisk) {
            $this->info('Skipping external disk configuration.');
        } else {
            $this->AddExternalDiskToConfig();
        }
    }

    private function createEnvFile(string $dbName): void
    {
        $content = 'APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

# PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE='.($dbName === null ? '' : $dbName).'
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
# CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=\'smtp.hostinger.com\'
MAIL_PORT=465
MAIL_USERNAME=""
MAIL_PASSWORD=\'\'
MAIL_FROM_ADDRESS=""
MAIL_FROM_NAME=""

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"';

        $filePath = base_path('.env');
        if (!file_exists($filePath)) {
            File::put($filePath, $content);

            $this->info('.env file created successfully.');
        }
        else {
            $this->info('.env file already exists. Skipping creation.');
        }

        $this->info('Generating application key.');
        exec('php artisan key:generate');
    }

    private function createDatabase(string $dbName, bool $skipMigrations): void
    {
        try {
          $conn = new \PDO("mysql:host=localhost", "root", "");
          // set the PDO error mode to exception
          $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch(\PDOException $e) {
          Log::error("Could not connect with database. " . $e->getMessage());
          return;
        }
    
        $sql = "CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        try {
          $conn->exec($sql);
          Log::info("Database created successfully");
        } catch(\PDOException $e) {
          // Handle errors during db creation
          Log::error("Error creating database: " . $sql . $e->getMessage());
          return;
        }

        if ($skipMigrations) {
            $this->info('Skipping migrations.');
        } else {
            //exec('php artisan migrate');
            $this->call('migrate', ['--force' => true]);
            $this->info('Migrations completed.');
        }
    }

    private function createDefaultUsers(): void
    {
        try {
            $userModel = config('overrides.models.User');
            if ($userModel::count() == 0) {
                $this->info('Creating default admin user.');
                $userModel::create([
                    'id' => 1,
                    'name' => 'admin',
                    'email' => 'admin@example.com',
                    'password' => Hash::make('admin'),
                ]);
            }

            $asesorModel = config('overrides.models.Asesor');
            $asesorId = 0;
            if ($asesorModel::count() == 0) {
                $this->info('Creating default asesor.');
                $asesorId = $asesorModel::insertGetId([
                    'id' => 1,
                    'name' => 'test',
                    'phone' => '3337811700',
                    'number' => '1111',
                    'password' => Hash::make('1111'),
                    'category' => 0,
                ]);
            }
            else {
                $asesorId = $asesorModel::first()->id;
            }
            /*asesorModel::firstOrCreate(
                ['mail' => 'test@example.com'],
                [
                    'id' => 1,
                    'name' => 'test',
                    'phone' => '3337811700',
                    'number' => '1111',
                    'password' => Hash::make('1111'),
                    'category' => 0,
                ]
            );*/
            
            $clientModel = config('overrides.models.Client');
            $clientId = 0;
            if ($clientModel::count() == 0) {
                $this->info('Creating default client.');
                $clientId = $clientModel::insertGetId([
                    'id' => 1,
                    'name' => 'test',
                    'phone' => '3337811700',
                    'mail' => 'test@example.com',
                    'asesor_id' => $asesorId
                ]);
            }
            else {
                $clientId = $clientModel::first()->id;
            }
            
            $quotationModel = config('overrides.models.Quotation');
            if ($quotationModel::count() == 0) {
                $this->info('Creating default quotation.');
                $quotationModel::create([
                    'client_id' => $clientId,
                    'medium' => '0',
                    'asesor_id' => $asesorId,
                    'unit_id' => '1',
                    'quoted_price' => '3474750.00',
                ]);
            }
        } catch (Exception $e) {
            $this->error("Error seeding: " . $e->getMessage());
        }
    }

    private function createDefaultQuotation(): void
    {
        try {
        } catch (Exception $e) {
            $this->error("Error seeding: " . $e->getMessage());
        }
    }

    private function publishAssets(): void
    {
        $this->info('Publishing assets.');

        $this->call('vendor:publish', [
            '--tag' => 'shared-utils-assets',
            '--force' => true,
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'listing-utils-assets',
            '--force' => true,
        ]);
    }

    private function AddExternalDiskToConfig(): void
    {
        $this->info('Adding external disk to config/filesystems.php.');

        $configPath = config_path('filesystems.php');
        $configContent = File::get($configPath);

        // Check if the external disk is already defined
        if (strpos($configContent, "'external' => [") !== false) {
            $this->info('External disk already defined in config/filesystems.php. Skipping modification.');
            return;
        }

        $extImgDir = $this->option('ext-img-dir');
        if ($extImgDir === null || $extImgDir === '') {
            $extImgDir = $this->ask('What is the name of your external image directory?');
        }

        // Define the new disk configuration
        $newDiskConfig = "
        'external' => [
            'driver' => 'local',
            'root' => '/',
            'url' => 'https://propstudios.mx/img/$extImgDir/',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],
    ";

        // Use regex to find the 'disks' array and insert the new disk configuration
        $pattern = "/('disks'.*=>.*\[)([\s\S]*?(?:.*=>.*\[[\s\S]*?],?[\s\S]*?)*)(\],?)/";
        $replacement = "$1$2$newDiskConfig$3";

        if (preg_match($pattern, $configContent)) {
            $newConfigContent = preg_replace($pattern, $replacement, $configContent);
            File::put($configPath, $newConfigContent);
            $this->info('External disk added to config/filesystems.php successfully.');
        } else {
            $this->error('Could not find the disks array in config/filesystems.php. Please add the following configuration manually:');
            $this->line($newDiskConfig);
        }
    }
}