<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\Command;

class SharedUtilsCommand extends Command
{
    public $signature = 'shared-utils';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
