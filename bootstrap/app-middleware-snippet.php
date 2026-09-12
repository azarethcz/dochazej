<?php
// Přidejte do bootstrap/app.php (Laravel 11) do bloku ->withMiddleware(function (Middleware $middleware) {

$middleware->alias([
    'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
]);

// Pokud používáte Laravel 10 nebo starší (app/Http/Kernel.php), přidejte místo toho
// do pole $middlewareAliases (resp. $routeMiddleware):
// 'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
