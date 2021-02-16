<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 10-Jan-20
 * Time: 17:46
 */

namespace App\Repository;

use App\Services\SessionTimer;
use DB;
use Illuminate\Support\Facades\Log;

class StatsRepository
{
    public static function orders($guard,$year,$period = null)
    {



       // $year != date('Y') ?: SessionTimer::check($year.'_stats_orders',1);


        if(session()->has($year.'_stats_orders')){
            Log::notice($year.'_orders coming from session');
            return session()->get($year.'_stats_orders');
        }
        else{
           
            $stats_orders =  DB::table('orders')
                ->where($guard.'_company_id',session()->get('company_id'))
                ->whereYear('created_at',$year)
                ->when($period == 'd', function ($query) {
                    return $query->whereMonth('created_at', date('m'));
                })
                ->get(['total_order_cost','currency','buyer_company_id','seller_company_id','seller_id','department','delivery_location_id','created_at','order'])
                ->sortBy('created_at');
            Log::notice($year.'_orders coming from table');

           // SessionTimer::put($year.'_stats_orders',$stats_orders);
             session()->put($year.'_stats_orders',$stats_orders);
           
            return $stats_orders;
        }
        
    }
    
    public static function stats($guard,$opposite_guard)
    {
        if(session()->has('stadts')) return session()->get('stats');
        else{
            $stats =    DB::table('purchase_sales_statistics')
                ->where($guard.'_company_id',session()->get('company_id'))
                ->get(['product_list','department','order_value',$opposite_guard.'_company_id as company_id','updated_at']);
            
            session()->put('stats',$stats);
            
            return $stats;
        }
       
    }
}
