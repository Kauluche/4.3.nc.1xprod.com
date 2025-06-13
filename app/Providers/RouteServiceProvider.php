<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Limit;
use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
        
        // Ajouter un log pour déboguer les routes enregistrées
        $this->logRegisteredRoutes();
    }
    
    /**
     * Méthode de débogage pour aider à comprendre les routes enregistrées
     * À supprimer en production
     */
    protected function logRegisteredRoutes(): void
    {
        // Ne s'exécute que dans l'environnement local
        if (app()->environment('local')) {
            \Illuminate\Support\Facades\Event::listen('router.matched', function () {
                $routes = collect(Route::getRoutes())->map(function ($route) {
                    return [
                        'domain' => $route->domain(),
                        'method' => implode('|', $route->methods()),
                        'uri' => $route->uri(),
                        'name' => $route->getName(),
                        'action' => ltrim($route->getActionName(), '\\'),
                        'middleware' => implode(', ', $route->middleware()),
                    ];
                });
                \Illuminate\Support\Facades\Log::info('Routes enregistrées', ['routes' => $routes->toArray()]);
            });
        }
    }
} 