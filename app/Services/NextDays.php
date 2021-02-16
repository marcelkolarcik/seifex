<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 29-Jul-19
 * Time: 21:28
 */

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
class NextDays
{
    public function get(    $num_of_days  )
    {
        $period = CarbonPeriod::create(Carbon::now(), $num_of_days, CarbonPeriod::EXCLUDE_START_DATE);
    
        $next_days = [];
        foreach ($period as $key => $date) {
            $next_days[$date->translatedFormat('l')] =
                [
                    'display_date'  =>  $date->translatedFormat('D d. M'),
                    'en_timestamp'  =>  $date->format('d-m-Y'),
                    'day_num'        =>  $date->format('N')
                ];
        }
        
        return $next_days;
    }
}
