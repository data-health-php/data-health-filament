<?php

declare(strict_types=1);

namespace DataHealth\Filament;

use DataHealth\Filament\Pages\FindingTypes;
use DataHealth\Filament\Resources\FindingRecords\FindingRecordResource;
use DataHealth\Filament\Widgets\FindingStatsOverview;
use Filament\Contracts\Plugin;
use Filament\Panel;

class DataHealthPlugin implements Plugin
{
    private bool $shouldUseBrandLogo = false;

    public static function make(): static
    {
        return app(static::class);
    }

    public function brandLogo(bool $condition = true): static
    {
        $this->shouldUseBrandLogo = $condition;

        return $this;
    }

    public function getId(): string
    {
        return 'data-health';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                FindingRecordResource::class,
            ])
            ->pages([
                FindingTypes::class,
            ])
            ->widgets([
                FindingStatsOverview::class,
            ]);

        if (! $this->shouldUseBrandLogo) {
            return;
        }

        $panel
            ->brandLogo(fn () => view()->file(__DIR__.'/../resources/views/components/logo.blade.php'))
            ->darkModeBrandLogo(fn () => view()->file(__DIR__.'/../resources/views/components/logo.blade.php', [
                'dark' => true,
            ]))
            ->brandLogoHeight('2rem');
    }

    public function boot(Panel $panel): void {}
}
