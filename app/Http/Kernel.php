<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'owner.auth' => \App\Http\Middleware\RedirectIfNotOwner::class,
        'owner.guest' => \App\Http\Middleware\RedirectIfOwner::class,
        'owner.verified' => \App\Http\Middleware\EnsureOwnerEmailIsVerified::class,
        'seller.auth' => \App\Http\Middleware\RedirectIfNotSeller::class,
        'seller.guest' => \App\Http\Middleware\RedirectIfSeller::class,
        'seller.verified' => \App\Http\Middleware\EnsureSellerEmailIsVerified::class,
        'buyer.auth' => \App\Http\Middleware\RedirectIfNotBuyer::class,
        'buyer.guest' => \App\Http\Middleware\RedirectIfBuyer::class,
        'buyer.verified' => \App\Http\Middleware\EnsureBuyerEmailIsVerified::class,
        'admin.auth' => \App\Http\Middleware\RedirectIfNotAdmin::class,
        'admin.guest' => \App\Http\Middleware\RedirectIfAdmin::class,
        'admin.verified' => \App\Http\Middleware\EnsureAdminEmailIsVerified::class,
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'seller_owner' => \App\Http\Middleware\Roles\SellerOwnerMiddleware::class,
        'seller_seller' => \App\Http\Middleware\Roles\SellerSellerMiddleware::class,
        'seller_accountant' => \App\Http\Middleware\Roles\SellerAccountantMiddleware::class,
        'buyer_owner' => \App\Http\Middleware\Roles\BuyerOwnerMiddleware::class,
        'buyer_buyer' => \App\Http\Middleware\Roles\BuyerBuyerMiddleware::class,
        'buyer_accountant' => \App\Http\Middleware\Roles\BuyerAccountantMiddleware::class,
        'can_access' => \App\Http\Middleware\CanAccessMiddleware::class,
        'buyer_seller'  =>  \App\Http\Middleware\Roles\BuyerSellerMiddleware::class,
        'buyer_can'  =>  \App\Http\Middleware\BuyerCanMiddleware::class,
        'seller_can'  =>  \App\Http\Middleware\SellerCanMiddleware::class,
        'buyer_seller_can'  =>  \App\Http\Middleware\BuyerSellerCanMiddleware::class,
        'is_owner' =>   \App\Http\Middleware\IsOwnerMiddleware::class,
        'admin_can'=>\App\Http\Middleware\AdminCanMiddleware::class,
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\Authenticate::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];
}
