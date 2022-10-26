<?php

namespace Rockero\CI\Commands;

trait LoadsCIConfig
{
    /** @var array{phpstan_level:int,min_test_coverage:int} */
    protected array $config;

    protected function loadConfig()
    {
        $this->config = json_decode(file_get_contents(base_path('ci.json')), true);
    }
}
