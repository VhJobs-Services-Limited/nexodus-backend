<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(config('app.env') == 'local');

        Model::handleMissingAttributeViolationUsing(function (Model $model, string $column) {
            $class = $model::class;

            info("Attempted to read missing column: [{$column}] on model [{$class}].");
        });


        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) {
            $class = $model::class;

            info("Attempted to lazy load [{$relation}] on model [{$class}].");
        });

        Model::handleDiscardedAttributeViolationUsing(function (Model $model, string $column) {
            $class = $model::class;

            info("Attempted to add disacrd column: [{$column}] on model [{$class}].");
        });

    }
}
