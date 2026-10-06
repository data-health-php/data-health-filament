<div align="center">
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="artwork/data-health-logo-dark.svg">
        <img src="artwork/data-health-logo.svg" alt="Data Health" width="720">
    </picture>
</div>

# Data Health Filament

A Filament 5 interface for [`data-health/data-health`](https://github.com/data-health-php/data-health).

It provides a Filament resource and dashboard widget for managing findings, as
well as standalone Livewire tables that can be embedded in your application.

## Documentation

For installation, usage, and configuration details, see the
[Data Health Filament documentation](https://data-health-php.github.io/data-health-docs/filament/overview/).

## Requirements

- PHP 8.3 or newer
- Laravel 12 or 13
- Filament 5
- `data-health/data-health` 1.x

## Installation

Install the package with Composer:

```bash
composer require data-health/data-health-filament
```

After installation, choose either the Panel Builder integration or the
standalone components. You can also use both in the same application.

## Quick start

Register the plugin in each Filament panel that should expose Data Health:

```php
use DataHealth\Filament\DataHealthPlugin;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel->plugin(DataHealthPlugin::make()->brandLogo());
}
```

Or render a standalone findings table in a Blade view without registering a
Filament panel:

```blade
<livewire:data-health-filament::finding-records-table />
```

To show findings for one model:

```blade
<livewire:data-health-filament::model-findings-table :model="$record" />
```

## Configuration

Publish the optional configuration file:

```bash
php artisan vendor:publish --tag=data-health-filament-config
```

The package does not perform authorization checks. Protect panels and routes
that expose Data Health with your application's authentication and authorization
middleware.

## Testing

```bash
composer test
```

## License

The MIT License.
