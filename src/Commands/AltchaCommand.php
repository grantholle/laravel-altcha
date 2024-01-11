<?php

namespace Grant Holle\Altcha\Commands;

use Illuminate\Console\Command;

class AltchaCommand extends Command
{
    public $signature = 'laravel-altcha';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
