<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Mail\EmailProviderInterface;
use App\Mail\Transport\CustomEmailTransport;
use App\Models\PersonalAccessToken;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind(EmailProviderInterface::class, function ($app): EmailProviderInterface {
            $providerClass = config('services.providers.email.services')[config('services.providers.email.default')];

            return $app->make($providerClass);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Mail::extend('custom', fn () => new CustomEmailTransport(app(EmailProviderInterface::class)));

        $this->configureCommands();
        $this->configureModels();
        $this->configureQueryLog();
        $this->configureRateLimit();
        $this->configureSanctumPersonalAccessToken();
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

        Schema::defaultStringLength(190);

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

    private function configureRateLimit()
    {
        RateLimiter::for('otp', fn (Request $request) => Limit::perMinute(3)->by($request->string('email') ?: $request->ip()));
    }

    private function configureSanctumPersonalAccessToken(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }

    /**
     * configure application's role and permissions
     */
    private function configureRoleAndPermissions(): void {}
}
