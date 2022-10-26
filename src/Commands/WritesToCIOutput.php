<?php

namespace Rockero\CI\Commands;

use Illuminate\Support\Facades\File;

trait WritesToCIOutput
{
    protected function appendToCIOutput(array $errors): void
    {
        $path = base_path('.github/ci-report.json');

        if (File::exists($path) && $json = trim(File::get($path))) {
            $json = json_decode($json);
            $json = array_merge($json, $errors);

            File::put($path, json_encode($json));

            return;
        }

        File::put($path, json_encode($errors));
    }

    protected function clearCIOutput(): void
    {
        $path = base_path('.github/ci-report.json');

        File::put($path, '');
    }
}
