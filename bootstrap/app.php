<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\{Exceptions, Middleware};

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', api: __DIR__.'/../routes/api.php', commands: __DIR__.'/../routes/console.php')
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.admin'           => \App\Http\Middleware\AuthAdmin::class,
            'auth.owner'           => \App\Http\Middleware\AuthOwner::class,
            'auth.tenant'          => \App\Http\Middleware\AuthTenant::class,
            'plan.limit'           => \App\Http\Middleware\PlanLimit::class,
            'active.subscription'  => \App\Http\Middleware\ActiveSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            $url = $request->url();
            if (str_contains($url, '/admin/'))  return redirect()->route('admin.login') ->withErrors(['email' => 'Session expired. Please log in again.']);
            if (str_contains($url, '/tenant/')) return redirect()->route('tenant.login')->withErrors(['email' => 'Session expired. Please log in again.']);
            return redirect()->route('owner.login')->withErrors(['email' => 'Session expired. Please log in again.']);
        });
    })->create();
