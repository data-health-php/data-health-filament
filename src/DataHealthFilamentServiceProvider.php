<?php

declare(strict_types=1);

namespace DataHealth\Filament;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class DataHealthFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/data-health-filament.php',
            'data-health-filament',
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'data-health-filament');

        $this->app->booted(fn () => Livewire::addNamespace(
            'data-health-filament',
            classNamespace: 'DataHealth\\Filament\\Livewire',
        ));

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/data-health-filament.php' => config_path('data-health-filament.php'),
        ], 'data-health-filament-config');
    }
}
