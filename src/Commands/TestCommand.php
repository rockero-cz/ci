<?php

namespace Rockero\CI\Commands;

use Illuminate\Console\Command;
use SebastianBergmann\Environment\Runtime;

class TestCommand extends Command
{
    use LoadsCIConfig;

    public $signature = 'ci:test';

    public function handle()
    {
        $this->loadConfig();

        $arguments = [];

        if ((new Runtime())->hasXdebug()) {
            $arguments[] = '--coverage';
            $arguments[] = '--min='.$this->config['min_test_coverage'];
        }

        return $this->call('test', $arguments);
    }
}
