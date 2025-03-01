<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
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
        $this->configureCommands();
        $this->configureModels();
        $this->configureQueryLog();
    }

    /**
     * Configure the application's commands.
     */
    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(
            $this->app->environment('production'),
        );
    }

    /**
     * Configure the application's query log.
     */
    private function configureQueryLog(): void
    {
        if (config('app.env') === 'local') {
            DB::listen(function (QueryExecuted $query): void {
                info($query->sql.'--context--'.json_encode($query->bindings).'--time--'.$query->time);
            });
        }
    }

    /**
     * Configure the application's models.
     */
    private function configureModels(): void
    {
        Model::shouldBeStrict();

        Model::unguard();

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

    /**
     * configure application's role and permissions
     */
    private function configureRoleAndPermissions(): void
    {
    }
}
