<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';
    
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //
        
        parent::boot();
    }
    
    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        
        $this->mapWebRoutes();
        
        $this->mapOwnerRoutes();
        
        $this->mapSellerRoutes();
        
        $this->mapBuyerRoutes();
        
        $this->mapAdminRoutes();
        
        //
    }
    
    /**
     * Define the "admin" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapAdminRoutes()
    {
        Route::prefix('admin')
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/admin.php'));
    }
    
    /**
     * Define the "buyer" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapBuyerRoutes()
    {
        Route::prefix('buyer')
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/buyer.php'));
    }
    
    /**
     * Define the "seller" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapSellerRoutes()
    {
        Route::prefix('seller')
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/seller.php'));
    }
    
    /**
     * Define the "owner" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapOwnerRoutes()
    {
        Route::prefix('owner')
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/owner.php'));
    }
    
    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }
    
    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
