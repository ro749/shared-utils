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
    {--ci}
    {--skip-env}
    {--skip-db-create}
    {--skip-migrate}
    {--skip-seed}
    {--skip-publish}';

    protected $description = 'Initializes the project by creating the .env file, creating a default admin user and publishing the necessary assets.';

    public function handle(): void
    {
        $isCi = $this->option('ci');

        $skipEnv = $this->option('skip-env') || $isCi;
        $skipDbCreate = $this->option('skip-db-create') || $isCi;
        $skipMigrate = $this->option('skip-migrate') || $isCi;
        $skipSeed = $this->option('skip-seed');
        $skipPublish = $this->option('skip-publish');


        $dbName = $this->option('db_name');
        if (!$isCi && ($dbName === null || $dbName === '')) {
            $dbName = $this->ask('What is the name of your database?');
        }

        if (! $skipDbCreate && ($dbName === null || $dbName === '' || ! preg_match('/^[A-Za-z0-9_]+$/', $dbName))) {
            $this->error('Invalid database name. If you don\'t want to create a database, use the --skip-db-create option.');
            return;
        }

        if ($skipEnv) {
            $this->info('Skipping .env file creation.');
        } else {
            $this->createEnvFile($dbName);
            exec('php artisan key:generate');
        }

        if ($skipDbCreate) {
            $this->info('Skipping db creation.');
        } else {
            $this->createDatabase($dbName, $skipMigrate);
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
        }
    }

    private function createDatabase(string $dbName, bool $skipMigrate): void
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

        if ($skipMigrate) {
            $this->info('Skipping migration.');
        } else {
            //exec('php artisan migrate');
            $this->call('migrate', ['--force' => true]);
        }
    }

    private function createDefaultUsers(): void
    {
        try {
            //$this->call('db:seed', ['--force' => true]);
            $userModel = config('overrides.models.User');

            if ($userModel::count() == 0) {
                $userModel::create([
                    'name' => 'admin',
                    'email' => 'admin@example.com',
                    'password' => Hash::make('admin'),
                ]);
            }

            $asesorModel = config('overrides.models.Asesor');

            $asesorModel::firstOrCreate(
                ['mail' => 'test@example.com'],
                [
                    'name' => 'test',
                    'phone' => '3337811700',
                    'number' => '1111',
                    'password' => Hash::make('1111'),
                    'category' => 0,
                ]
            );
        } catch (Exception $e) {
            $this->error("Error seeding: " . $e->getMessage());
        }
    }

    private function createDefaultQuotation(): void
    {
        try {
            $this->info('Creating Quotation.');
            $quotationModel = config('overrides.models.Quotation');
            if ($quotationModel::count() == 0) {
                $quotationModel::create([
                    'client_id' => '1',
                    'medium' => '0',
                    'assesor_id' => '1',
                    'unit_id' => '1',
                    'quoted_price' => '3474750.00',
                ]);
            }
        } catch (Exception $e) {
            $this->error("Error seeding: " . $e->getMessage());
        }
    }

    private function publishAssets(): void
    {
        $this->call('vendor:publish', [
            '--tag' => 'shared-utils-assets',
            '--force' => true,
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'listing-utils-assets',
            '--force' => true,
        ]);
    }
}