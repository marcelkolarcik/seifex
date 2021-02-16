<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 29-Jul-19
 * Time: 18:39
 */

namespace App\Services;
use App\Services\BrowserLanguages;
use Carbon\Carbon;

class CarbonLocale
{
    public $browserLanguage;
    
    public function __construct(BrowserLanguages $browserLanguage)
    {
        $this->browserLanguage  =   $browserLanguage;
    }
    
    public function set()
   {
       Carbon::setLocale($this->browserLanguage->setBrowserMatch());
   }
}
