<?php

declare(strict_types=1);

namespace DataHealth\Filament\Support;

use DataHealth\Models\FindingRecord;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

final class ModelViewUrl
{
    public static function resolve(FindingRecord $findingRecord): ?string
    {
        $modelClass = $findingRecord->model()->getRelated()::class;
        $routes = config('data-health-filament.model_view_routes', []);
        $routeName = $routes[$modelClass] ?? $routes[$findingRecord->model_type] ?? null;

        if (is_string($routeName) && Route::has($routeName)) {
            $model = self::relatedModel($findingRecord);

            return $model ? route($routeName, [$model]) : null;
        }

        if ($resolver = self::configuredResolver()) {
            $model = self::relatedModel($findingRecord);

            if (! $model) {
                return null;
            }

            $url = $resolver($model);

            return is_string($url) && filled($url) ? $url : null;
        }

        $panel = Filament::getCurrentPanel();

        if (! $panel) {
            return null;
        }

        $model = self::relatedModel($findingRecord);

        if (! $model) {
            return null;
        }

        $resource = $panel->getModelResource($model);

        if (
            ! $resource ||
            ! $resource::hasPage('view') ||
            ! $resource::canView($model)
        ) {
            return null;
        }

        return $resource::getUrl(
            'view',
            ['record' => $model],
            panel: $panel->getId(),
        );
    }

    public static function shouldEagerLoadModel(): bool
    {
        return filled(config('data-health-filament.model_view_routes', [])) ||
            filled(config('data-health-filament.model_view_url_resolver')) ||
            Filament::getCurrentPanel() !== null;
    }

    /** @return (callable(Model): ?string)|null */
    private static function configuredResolver(): ?callable
    {
        $resolver = config('data-health-filament.model_view_url_resolver');

        if (is_string($resolver) && class_exists($resolver)) {
            $resolver = app($resolver);
        }

        return is_callable($resolver) ? $resolver : null;
    }

    private static function relatedModel(FindingRecord $findingRecord): ?Model
    {
        $model = $findingRecord->getRelationValue('model');

        return $model instanceof Model ? $model : null;
    }
}
