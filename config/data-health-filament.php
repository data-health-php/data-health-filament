<?php

declare(strict_types=1);

return [
    'navigation_group' => 'Data Health',
    'navigation_sort' => 100,
    'polling_interval' => null,

    /*
     * Optionally provide an invokable class name which returns the model types
     * that may be assigned to a finding. The class must return an array of
     * Filament MorphToSelect Type instances.
     */
    'assignee_types' => null,

    /*
     * Map model classes to the named route for their single-record view.
     * The related model is passed as the route's first positional parameter.
     */
    'model_view_routes' => [],

    /*
     * Optionally provide a callable, or an invokable class name, which returns
     * a URL for a model. Route mappings above take precedence over it.
     */
    'model_view_url_resolver' => null,
];
