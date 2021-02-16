<?php

namespace App\Providers;




use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Services\BrowserLanguages;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    
    public $browserLanguage;
    /**
     * Register any application services.
     *
     * @return void
     */
   
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(BrowserLanguages $browserLanguage)
    {
        
        Schema::defaultStringLength(191);
        $this->browserLanguage  =   $browserLanguage;
        App::setLocale( $this->browserLanguage->setBrowserMatch() );
       
    }
}
