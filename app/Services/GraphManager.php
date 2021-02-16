<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 10-Jan-20
 * Time: 17:39
 */

namespace App\Services;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GraphManager extends GraphMaker
{
    public static function graph($type,$guard,$opposite_guard,$company,$year,$top_products,$period = null)
    {


        $graph_type =  self:: graph_type($type,$year,$top_products,$period);
        /*IF WE HAVE GRAPH ALREADY IN SESSION, WE'LL GET IT FROM SESION*/


        /*CHECK CURRENT YEAR GRAPH TIME IN SESSION, IF LONGER THEN minutes PULL FROM THE SESSION
        , TO KEEP  CURRENT YEAR'S GRAPHS UP TO DATE WITH ORDERS COMING IN LIVE*/
        $year != date('Y') ?: SessionTimer::check($graph_type,1,true);


        if(session()->has($graph_type) )
        {
            Log::info('session '.$graph_type);

            return session()->get($graph_type);
        }
        /*IF USER IS LOOKING FOR PREVIOUS YEARS*/
        elseif($year < date('Y'))
        {
            /*, WE SHOULD HAVE GRAPH STORED IN DB, NO NEED FOR
                COMPUTING AGAIN*/
            if(
            $full_graph =
                DB::table('graphs')
                    ->where
                    ([
                        'company_id' =>  session()->get('company_id'),
                        'guard'      =>  $guard
                    ])
                    ->whereJsonLength('graph->'.$graph_type, '>',0)
                    ->pluck('graph')
                    ->first()
            )
            {

                Log::info('table '.$graph_type);

                $graph = json_decode( $full_graph,true) ;

                SessionTimer::put($graph_type,$graph[$graph_type]);
               // session()->put($graph_type,$graph[$graph_type]   );


                return $graph[$graph_type];
            }
            /*IF IT IS FIRST REQUEST FOR PREVIOUS YEAR, WE'LL CREATE GRAPH, STORE IT IN DB AND
            PUT IT IN SESSION TO SAVE ON COMPUTING*/
            else
            {
                $graph =  self::create_graph($type,$guard,$opposite_guard,$company,$year,$top_products,$period);


                if(!DB::table('graphs')
                    ->where
                    ([
                        'company_id' =>  session()->get('company_id'),
                        'guard'      =>  $guard
                    ])
                    ->update([
                        'graph->'.$graph_type => json_encode($graph,true),
                        'updated_at'          =>  date('Y-m-d H:i:s')]) )
                {
                    $t_graph[$graph_type] = $graph ;

                    DB::table('graphs')->insert([
                        'company_id'    =>  session()->get('company_id'),
                        'guard'         =>  $guard,
                        'graph'         =>  json_encode($t_graph),
                        'created_at'    =>  date('Y-m-d H:i:s'),
                        'updated_at'    =>  date('Y-m-d H:i:s'),
                    ]);
                }

                Log::warning('fresh '.$graph_type);

                return  $graph;
            }

        }
        /* IT IS CURRENT YEAR, WE WILL COMPUTE GRAPH AND STORE IT IN SESSION FOR USE */
        else
        {
            Log::info('fresh '.$graph_type);
            return self::create_graph($type,$guard,$opposite_guard,$company,$year,$top_products,$period);
        }


    }
    /////////////////// HELPERS
    static  function  create_graph($type,$guard,$opposite_guard,$company,$year,$top_products,$period)
    {
        $graph = parent::$type($guard,$opposite_guard,$company,$year,$top_products,$period);
        $graph_type = self::graph_type($type,$year,$top_products,$period);

        SessionTimer::put($graph_type,$graph );
       // session()->put($graph_type,$graph   );

        return $graph;
    }
    
    static function graph_type($type,$year,$top_products,$period)
    {
       return   $year.'_'.session()->get('company_id').'_'.$type.'_'.$period.'_'.$top_products;
    }
    
}
