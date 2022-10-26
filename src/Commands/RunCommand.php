<?php

namespace Rockero\CI\Commands;

use Illuminate\Console\Command;
use RuntimeException;

class RunCommand extends Command
{
    use WritesToCIOutput;

    public $signature = 'ci:run';

    public function handle()
    {
        $this->clearCIOutput();

        $this->components->info('Running tests...');
        $tests = $this->call('ci:test');

        $this->components->info('Running PHPStan...');
        $phpstan = $this->call('ci:phpstan', ['--ansi']);

        $this->components->info('Running linter...');
        $lint = $this->call('ci:lint', ['--ansi']);

        if ($tests > 0 || $phpstan > 0 || $lint > 0) {
            $this->outputErrorSummary($tests, $phpstan, $lint);

            return Command::INVALID;
        }

        $this->output->writeLn("\n  <fg=white;bg=green;options=bold> PASS </><fg=default> All checks passed.</>");

        $this->newLine();
    }

    /**
     * Try running an artisan command.
     */
    protected function try($command): void
    {
        if ($this->call($command) > 0) {
            throw new RuntimeException;
        }
    }

    /**
     * Output the summary of failed checks.
     */
    protected function outputErrorSummary($tests, $phpstan, $lint): void
    {
        $this->output->writeLn("\n  <fg=black;bg=red;options=bold> FAIL </><fg=default> Some checks failed.</>");

        $this->newLine();

        $getIcon = function ($exitCode) {
            return $exitCode > 0
                ? '<fg=red;options=bold>⨯</><fg=default>'
                : '<fg=green;options=bold>✓</><fg=default>';
        };

        $this->output->writeLn("  {$getIcon($tests)} \e[2mTests\e[22m</>");
        $this->output->writeLn("  {$getIcon($phpstan)} \e[2mPHPStan\e[22m</>");
        $this->output->writeLn("  {$getIcon($lint)} \e[2mLinter\e[22m</>");

        $this->newLine();
    }
}
