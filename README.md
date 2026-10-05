<div align="center">
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="artwork/data-health-logo-dark.svg">
        <img src="artwork/data-health-logo.svg" alt="Data Health" width="720">
    </picture>
</div>

# Data Health Filament

A Filament 5 interface for [`data-health/data-health`](https://github.com/data-health-php/data-health).

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

## Panel Builder

Register the plugin in each Filament panel that should expose Data Health:

```php
use DataHealth\Filament\DataHealthPlugin;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel->plugin(DataHealthPlugin::make()->brandLogo());
}
```

The plugin adds:

- a searchable and filterable Finding records resource;
- verify, resolve, ignore, and reopen actions;
- bulk ignore and reopen actions;
- a Finding type catalogue with manual detection actions;
- an active, immediate, resolved, and ignored Findings overview widget.

Calling `brandLogo()` uses the Data Health logo in that panel. Omit the call when
the panel should retain your application's own branding.

The package does not perform authorization checks. Restrict access to the panel
with your application's normal authentication and authorization middleware.

## Standalone components

You do not need to create or register a Filament panel to use the standalone
Livewire components.

Display all findings in any Blade view:

```blade
<livewire:data-health-filament::finding-records-table />
```

Display only the findings belonging to a model:

```blade
<livewire:data-health-filament::model-findings-table :model="$record" />
```

The table is scoped by the model's morph class and primary key, and exposes the
same record actions as the main resource.

When the components are rendered outside a Filament panel layout, include
Filament's assets in your application layout:

```blade
<head>
    @filamentStyles
</head>

<body>
    {{ $slot }}

    @filamentScripts
</body>
```

The standalone components use the same finding actions as the Panel Builder
integration. Protect the route that renders the components with your
application's normal authentication and authorization middleware.

## Configuration

Publish the optional configuration file:

```bash
php artisan vendor:publish --tag=data-health-filament-config
```

It controls the navigation group, navigation sort position, dashboard widget
polling interval, and links from findings to their affected models.

### Assignees

To make finding assignees available in both the panel resource and standalone
components, configure an invokable provider class:

```php
'assignee_types' => App\DataHealth\AssigneeTypes::class,
```

The provider returns Filament morph-to select types. Use
`modifyOptionsQueryUsing()` to restrict the records available for a type:

```php
namespace App\DataHealth;

use App\Models\Group;
use App\Models\User;
use Filament\Forms\Components\MorphToSelect\Type;
use Illuminate\Database\Eloquent\Builder;

final class AssigneeTypes
{
    /** @return array<Type> */
    public function __invoke(): array
    {
        return [
            Type::make(User::class)
                ->titleAttribute('name')
                ->modifyOptionsQueryUsing(
                    fn (Builder $query): Builder => $query->active(),
                ),
            Type::make(Group::class)
                ->titleAttribute('name'),
        ];
    }
}
```

Keeping only the provider class name in configuration allows Laravel's
configuration cache to be used. Returning native Filament types also supports
custom labels, search columns, and option label callbacks.

Map a model to the named route for its single-record view:

```php
use App\Models\Customer;
use App\Models\Order;

'model_view_routes' => [
    Customer::class => 'customers.show',
    Order::class => 'orders.show',
],
```

The model is passed as the route's first positional parameter, so route
parameters may use model-specific names such as `{customer}` or `{order}`.

For models requiring custom URL generation, configure an invokable resolver:

```php
'model_view_url_resolver' => App\Support\GetModelViewUrl::class,
```

The resolver receives the affected model and returns its URL or `null`. Model
route mappings take precedence over the resolver. If neither produces a URL,
the current Filament panel's resource view is used when available. Use an
invokable class instead of a closure so Laravel's configuration cache remains
supported.

## Testing

```bash
composer test
```

## License

The MIT License.
