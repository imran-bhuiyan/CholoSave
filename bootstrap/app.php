<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust the local reverse proxy (Nginx on the same host) so Laravel
        // sees the real client IP and generates https:// URLs behind TLS.
        // Loopback only — trusting '*' would let clients spoof
        // X-Forwarded-For and defeat IP-based rate limiting. If the proxy
        // runs elsewhere (load balancer, Cloudflare), replace with its IP.
        $middleware->trustProxies(at: ['127.0.0.1', '::1']);

        $middleware->alias([
            'site_admin' => \App\Http\Middleware\EnsureUserIsSiteAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
