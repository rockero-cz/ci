<?php

namespace Rockero\CI\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Rockero\Linter\Data\LintError;
use Rockero\Linter\Linter;

class LintCommand extends Command
{
    use WritesToCIOutput;

    public $signature = 'ci:lint';

    public function handle()
    {
        $errors = collect();

        Linter::run(function (LintError $error) use (&$errors) {
            $this->displayError($error);

            $errors->push($error);
        });

        if ($errors->isNotEmpty()) {
            $this->appendToCIOutput($errors->toArray());

            $this->output->error($errors->count().' '.Str::plural('problem', $errors->count()).' found');

            return Command::INVALID;
        }

        $this->output->success('No problems found');
    }

    protected function displayError(LintError $error): void
    {
        $this->output->writeLn("\n  <fg=black;bg=red;options=bold> FAIL </><fg=default> {$error->file}:{$error->line}</>");
        $this->output->writeLn("  <fg=red;options=bold>⨯</><fg=default> \e[2m{$error->message}\e[22m</>");
    }
}
