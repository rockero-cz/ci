<?php

namespace Rockero\CI\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class PHPStanCommand extends Command
{
    use WritesToCIOutput;
    use LoadsCIConfig;

    public $signature = 'ci:phpstan';

    public function handle()
    {
        $this->loadConfig();

        $process = $this->runProcess($this->commandString());
        $exitCode = $process->getExitCode();

        if ($exitCode > 0) {
            $this->logErrorsForCI();
        }

        return $exitCode;
    }

    protected function commandString(): string
    {
        return 'vendor/bin/phpstan --level='.$this->config['phpstan_level'];
    }

    /**
     * Log phpstan output for CI.
     */
    protected function logErrorsForCI(): void
    {
        $process = $this->runProcess($this->commandString().' --error-format=json', displayOutput: false);

        if (! Str::isJson($process->getOutput())) {
            // Terminate the command.

            exit;
        }

        $this->appendToCIOutput($this->formatErrors(json_decode($process->getOutput(), true)));
    }

    /**
     * Reformat phpstan errors.
     */
    protected function formatErrors(array $errors): array
    {
        return collect($errors['files'])
            ->map(function ($data, $file) {
                foreach ($data['messages'] as &$message) {
                    unset($message['ignorable']);

                    $message['file'] = Str::after($file, base_path().'/');
                }

                return $data;
            })
            ->pluck('messages')
            ->flatten(1)
            ->all();
    }

    /**
     * Run shell command and optionally display output.
     */
    protected function runProcess(string $command, bool $displayOutput = true): Process
    {
        $process = Process::fromShellCommandline($command);

        $process->setTimeout(null);

        $process->setPty($displayOutput);

        $process->run(function ($type, $buffer) use (&$output, $displayOutput) {
            $output .= $buffer;

            if ($displayOutput) {
                $this->output->write((string) $buffer);
            }
        });

        return $process;
    }
}
