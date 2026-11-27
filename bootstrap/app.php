<?php

use App\Http\Controllers\ShellController;
use App\Http\Middleware\EnsureCanBook;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'book' => EnsureCanBook::class,
        ]);

        // The INR / NPR choice is set by the website app in the browser, so it is a plain cookie.
        $middleware->encryptCookies(except: ['currency']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // A website address that does not exist: the app's "page not found", with a 404 status.
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*', 'admin', 'admin/*', 'livewire/*', 'storage/*', 'app/*') || $request->expectsJson()) {
                return null;
            }

            return ShellController::notFound();
        });
    })->create();
