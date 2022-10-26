<?php

namespace Rockero\CI;

use Illuminate\Support\Facades\File;
use Rockero\CI\Commands\LintCommand;
use Rockero\CI\Commands\PHPStanCommand;
use Rockero\CI\Commands\RunCommand;
use Rockero\CI\Commands\TestCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CIServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('ci')
            ->hasCommand(LintCommand::class)
            ->hasCommand(PHPStanCommand::class)
            ->hasCommand(RunCommand::class)
            ->hasCommand(TestCommand::class);
    }

    public function boot()
    {
        parent::boot();

        $filesToPublish = collect(File::allFiles(__DIR__.'/../stubs', true))
            ->mapWithKeys(fn ($file) => [$file->getPathname() => $this->app->basePath($file->getRelativePathname())])
            ->toArray();

        $this->publishes($filesToPublish, "{$this->package->shortName()}-config");
    }
}
