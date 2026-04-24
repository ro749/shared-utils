<?php

namespace Ro749\SharedUtils\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class InitProject extends Command
{
    protected $signature = 'init:project {--db_name=}';

    protected $description = 'Initializes the project by creating the .env file, creating a default admin user and publishing the necessary assets.';

    public function handle(): void
    {
        $dbName = $this->option('db_name');
        if ($dbName === null || $dbName === '') {
            $dbName = $this->ask('What is the name of your database?');
        }
        
        $content = 'APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:cblPWWlOqTsn9xIZ4HxfniiAOk4NvBUXFkIOsnfas0g=
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
MAIL_USERNAME="cotizacion@propstudios.mx"
MAIL_PASSWORD=\'&i)Gq8!Z;&Gk\'
MAIL_FROM_ADDRESS="cotizacion@propstudios.mx"
MAIL_FROM_NAME="PROPSTUDIOS"

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

        if ($dbName !== null && $dbName !== '') {
            try {
              $conn = new \PDO("mysql:host=localhost", "root", "");
              // set the PDO error mode to exception
              $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch(\PDOException $e) {
              Log::error("Could not connect. " . $e->getMessage());
            }
    
            try {
              $sql = "CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
              $conn->exec($sql);
              Log::info("Database created successfully");
            } catch(\PDOException $e) {
              // Handle errors during db creation
              Log::error("Error creating database: " . $sql . $e->getMessage());
            }

            exec('php artisan migrate');
        }

        try {
            $this->call('generate:overrides');
            if(config('overrides.models.User')::count() == 0) {
                config('overrides.models.User')::create([ 'name' => 'admin', 'email' => 'admin@example.com', 'password' => Hash::make('admin')]);
            }
            config('overrides.models.Asesor')::create([ 
                'name' => 'test', 
                'mail' => 'test@example.com', 
                'phone' => '3337811700',
                'number' => '1111',
                'password' => Hash::make('1111'),
                'category' => 0
            ]);

        }
        catch(Exception $e) {
            $this->error("Error seeding: " . $e->getMessage());
        }

        $this->call('vendor:publish', ['--tag' => 'shared-utils-assets', '--force' => true]);
        $this->call('vendor:publish', ['--tag' => 'listing-utils-assets', '--force' => true]);
    }
}