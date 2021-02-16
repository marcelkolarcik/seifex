<?php

namespace App\Providers;

//u/se App\Services\BrowserLanguages;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
//use Illuminate\Support\Facades\App;

class ViewServiceProvider extends ServiceProvider
{
    
    
   
    
   
   /* public $browserLanguage;*/
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(/*BrowserLanguages $browserLanguage*/)
    {
      /*  $this->browserLanguage  =   $browserLanguage;
      
        View::composer('*', function () {
           
            App::setLocale( $this->browserLanguage->setBrowserMatch() );
        });*/
    }
}
