<?php

declare(strict_types=1);

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api/v1.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix(str_contains(request()->getHost(), 'api.') ? 'v1' : 'api/v1')
                ->name('api.')
                ->group(base_path('routes/api/v1.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api([
            Spatie\ResponseCache\Middlewares\CacheResponse::class,
        ]);
        $middleware->alias([
            'doNotCacheResponse' => Spatie\ResponseCache\Middlewares\DoNotCacheResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(fn (ModelNotFoundException $e) => response()->json([
            'message' => 'Record not found.',
        ], 404));

        $exceptions->render(fn (AuthenticationException $e) => response()->json([
            'message' => 'The token is invalid or has expired, please login to continue',
        ], 401));

        $exceptions->render(function (QueryException $e, Request $request) {
            if ($request->expectsJson()) {
                $error = when(app()->environment(['local', 'staging', 'dev']), [
                    'message' => mysql_error_msg($e->getCode()),
                    'sql' => $e->getMessage(),
                    'exception' => implode('/', array_slice(explode('\\', $e::class), -2)),
                    'file' => str_replace('.php', '', implode('/', array_slice(explode('/', $e->getFile()), -2))),
                    'line' => $e->getLine(),
                ], [
                    'message' => mysql_error_msg($e->getCode()),
                ]);

                return response()->json($error, $e instanceof HttpException ? $e->getStatusCode() : 500);
            }

            return null;
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                $error = when(app()->environment(['local', 'staging', 'dev']), [
                    'message' => $e->getMessage(),
                    'exception' => implode('/', array_slice(explode('\\', $e::class), -2)),
                    'file' => str_replace('.php', '', implode('/', array_slice(explode('/', $e->getFile()), -2))),
                    'line' => $e->getLine(),
                ], [
                    'message' => $e->getMessage(),
                ]);

                return response()->json($error, $e instanceof HttpException ? $e->getStatusCode() : 500);
            }

            return null;
        });

        $exceptions->shouldRenderJsonWhen(fn (Request $request, Throwable $e) => $request->expectsJson());
    })->create();
