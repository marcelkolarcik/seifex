<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 10-Jan-20
 * Time: 17:39
 */

namespace App\Services;

use App\Repository\StatsRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Self_;

class GraphMaker
{
    
    
    /*AMOUNTS*/
    static function by_departments($guard,$opposite_guard,$company,$year)
    {
        $orders = self::orders($guard,$year,'department');
        $colors = [];
        $by_departments = [];
        
        foreach($orders as $department   =>  $dep_orders)
        {
            foreach($dep_orders as $order)
            {
                $department_stats[$department][]  =   $order->total_order_cost;
            }
            $by_departments[$department] = round( array_sum( $department_stats[$department] ),2);
            $colors[]   =   self::random_color();
        }
       
        return self::yearly_graph($by_departments,$colors,__FUNCTION__,$year);
      
    }
    static function by_locations($guard,$opposite_guard,$company,$year)
    {
        $orders = self::orders($guard,$year,'delivery_location_id');
        $colors = [];
        $by_locations = [];
       
        foreach($orders as $dl_id   =>  $loc_orders)
        {
            foreach($loc_orders as $order)
            {
                $department   =   $order->department;
                $delivery_location_path = array_key_first($company->delivery_locations[$department][$dl_id]['path'])
                    .'?'.array_values($company->delivery_locations[$department][$dl_id]['path'])[0] ;
                
                $location_stats[$delivery_location_path][] = $order->total_order_cost;
            }
            $stats_by_locations[$delivery_location_path]['total'] = array_sum( $location_stats[$delivery_location_path]);
            $by_locations[ explode('?',$delivery_location_path) [0] ] = round(array_sum( $location_stats[$delivery_location_path]),2) ;
            $colors[]   =   self::random_color();
        }
        
        return self::yearly_graph($by_locations,$colors,__FUNCTION__,$year);
       
    }
    static function by_companies($guard,$opposite_guard,$company,$year)
    {
        $orders = self::orders($guard,$year,$opposite_guard.'_company_id');
        $companies   =   $opposite_guard.'_companies';
        $by_companies_o = [];
        $colors = [];
        $by_companies = [];
        
        foreach($orders as $bc_id   =>  $com_orders)
        {
            foreach($com_orders as $order)
            {
                $by_companies_o[$company->$companies[$bc_id]['company_name']] [] = $order->total_order_cost;
            }
            $company_total = round(array_sum( $by_companies_o[$company->$companies[$bc_id]['company_name']]),2);
            $by_companies[$company->$companies[$bc_id]['company_name']]=  $company_total;
            $colors[]   =   self::random_color();
        }
       
        return self::yearly_graph($by_companies,$colors,__FUNCTION__,$year);
    }
    static function by_sellers($guard,$opposite_guard,$company,$year)
    {
        $orders = self::orders($guard,$year,'seller_id');
        $seller_names = DB::table('sellers')
            ->whereIn('id',array_keys($orders))
            ->pluck('name','id')
            ->toArray();
        $colors = [];
        $by_sellers = [];
        
        foreach($orders as $seller_id   =>  $seller_orders)
        {
            foreach($seller_orders as $order)
            {
                $by_sellers[$seller_names[$seller_id]] [] = $order->total_order_cost;
            }
            $sellers_total = round(array_sum( $by_sellers[$seller_names[$seller_id]]),2);
            $by_sellers[$seller_names[$seller_id]]=  $sellers_total;
            $colors[]   =   self::random_color();
        }
        
        return self::yearly_graph($by_sellers,$colors,__FUNCTION__,$year);
    }
    static function by_products($guard,$opposite_guard,$company,$year,$top_products)
    {
       
        $orders = self::orders($guard,$year);
        $product_sales =[];
        $colors = [];
        $by_products = [];
        
        foreach($orders as $order)
        {
            $products_o[]  =  json_decode($order->order,true)  ;
        }
        foreach($products_o   as $key => $ordered_products)
        {
           
            foreach($ordered_products as $product_name => $data)
            {
                $product_sales[$product_name][] = $data['total_product_price'];
                $by_products[$product_name] = round( array_sum($product_sales[$product_name]),2)  ;
            }
            $colors[] = self::random_color();
        }
       
        $by_products = array_slice($by_products, 0, $top_products);
      
        return self::yearly_graph($by_products,$colors,__FUNCTION__,$year,$top_products);
    }
    /*TIME PERIODS*/
    static function by_department_periods($guard,$opposite_guard,$company,$year,$top_products,$period)
    {
       
        $orders = self::period_orders($guard,$year,$period,'department') ;
      
        $colors = [];
        $total = [];
        $all_deps = [];
        $date_sums = [];
        $period_departments = [];
        $datasets = [];
        $labels = array_keys($orders);
        
        foreach($orders as $date =>    $departments)
        {
            foreach($departments as $department =>  $d_orders)
            {
                $sum = round(array_sum(array_column($d_orders, 'total_order_cost')),2) ;
                $total[] = $sum;
                $date_sums[$date][$department]    = $sum;
                $all_deps[$department] = 1;
            }
        }
        $total = array_sum($total);
        $departments = array_keys($all_deps);
        
        /*FINDING EMPTY GAPS IN TIME PERIODS, FOR EXAMPLE IF WE CREATED NEW COMPANY LATER IN
     COUPLE OF MONTHS, NEW COMPANY WOULDN'T HAVE DATA FROM PREVIOUS  PERIODS AS OLDER COMPANY WOULD HAVE
      */
        $department_data = self::fill_the_gaps($date_sums,$departments);
        $percentage_colors = [];
        
        foreach($department_data as $department =>    $data)
        {
            $period_departments[$department]    =   array_sum($data);
            $color = self::random_color();
            $percentage_colors[str_replace('_',' ',$department)]    =  $color;
            $datasets[] =
                [
                    'data'=> $data,
                    'label'=> $department,
                    'borderColor'=> $color,
                    'fill'=> false
                ];
        }
       
        arsort($period_departments);
        $percentage = self::labels($period_departments,$total)['percentage'];
        
        return self::by_months_graph(
            array_values($datasets),
            $labels,
            $colors,
            'line',
            self::graph_title($year,$top_products,$period)[__FUNCTION__],
            $percentage,
            $percentage_colors,
            true);
    }
    static function by_location_periods($guard,$opposite_guard,$company,$year,$top_products,$period)
    {
        $orders = self::period_orders($guard,$year,$period,'delivery_location_id') ;
        $colors = [];
        $total = [];
        $locations = [];
        $period_locations = [];
        $datasets = [];
        $labels  =  array_keys($orders);
        $date_sums  =   [];
        $location_index = $company->delivery_locations_index;
        
        foreach($orders as $date   =>    $dl_ids)
        {
            foreach($dl_ids as $dl_id =>  $dl_orders)
            {
                $location_name = array_key_first($location_index[$dl_id]) ;
                $sum = round(array_sum(array_column($dl_orders, 'total_order_cost')),2) ;
                $date_sums[$date][$location_name][]   = $sum;
                $locations[$location_name]  = 1;
                $total[] = $sum;
            }
        }
        
        $locations  =   array_keys($locations);
        $total = array_sum($total);
        
        /*FINDING EMPTY GAPS IN TIME PERIODS, FOR EXAMPLE IF WE CREATED NEW COMPANY LATER IN
     COUPLE OF MONTHS, NEW COMPANY WOULDN'T HAVE DATA FROM PREVIOUS  PERIODS AS OLDER COMPANY WOULD HAVE
      */
        $location_data = self::fill_the_gaps($date_sums,$locations);
        
        foreach($location_data as $location_name =>    $data)
        {
            $period_locations[$location_name]    =   array_sum($data);
            $color = self::random_color();
            $percentage_colors[$location_name]    =   $color;
            $datasets[] =
                [
                    'data'=> $data,
                    'label'=> $location_name,
                    'borderColor'=> $color,
                    'fill'=> false
                ];
        }
        arsort($period_locations);
        
        $percentage = self::labels($period_locations,$total)['percentage'];
      
        
        return self::by_months_graph(
            array_values($datasets),
            $labels,
            $colors,
            'line',
            self::graph_title($year,$top_products,$period)[__FUNCTION__],
            $percentage,
            $percentage_colors,
            true);
    }
    static function by_company_periods($guard,$opposite_guard,$company,$year,$top_products,$period)
    {
        $orders = self::period_orders($guard,$year,$period,$opposite_guard.'_company_id') ;
        $colors = [];
        $total = [];
        $companies = [];
        $date_sums = [];
        $period_companies = [];
        $datasets = [];
        $labels  =   array_keys($orders);
        $opposite_company_names = $opposite_guard.'_companies';
        $opposite_companies = $company->$opposite_company_names;
      
        foreach($orders as $date   =>    $company_ids)
        {
            foreach($company_ids as $company_id =>  $bc_orders)
            {
                $sum = round(array_sum(array_column($bc_orders, 'total_order_cost')),2) ;
                $date_sums[$date][$company_id]    = $sum;
                $companies[$company_id]  = 1;
                $total[] = $sum;
            }
        }
        
        $companies  =   array_keys($companies);
        $total = array_sum($total);
        
        /*FINDING EMPTY GAPS IN TIME PERIODS, FOR EXAMPLE IF WE CREATED NEW COMPANY LATER IN
     COUPLE OF MONTHS, NEW COMPANY WOULDN'T HAVE DATA FROM PREVIOUS  PERIODS AS OLDER COMPANY WOULD HAVE
      */
        $company_data = self::fill_the_gaps($date_sums,$companies);
       
        foreach($company_data as $company_id =>    $data)
        {
            $company_name = $opposite_companies[$company_id]['company_name'];
            $period_companies[$company_name]    =   array_sum($data);
            $color = self::random_color();
            $percentage_colors[$company_name] = $color;
            $datasets[] =
                [
                    'data'=> $data,
                    'label'=> $company_name,
                    'borderColor'=> $color,
                    'fill'=> false
                ];
        }
        arsort($period_companies);
        $percentage = self::labels($period_companies,$total)['percentage'];
        
        return self::by_months_graph(
            array_values($datasets),
            $labels,
            $colors,
            'line',
            self::graph_title($year,$top_products,$period)[__FUNCTION__],
            $percentage,
            $percentage_colors,
            true);
        
    }
    static function by_seller_periods($guard,$opposite_guard,$company,$year,$top_products,$period)
    {
        $orders = self::period_orders($guard,$year,$period,'seller_id') ;
        $colors = [];
        $total = [];
        $sellers = [];
        $date_sums = [];
        $period_sellers = [];
        $datasets = [];
        $labels  =   array_keys($orders);
        
        foreach($orders as $date   =>    $seller_ids)
        {
            foreach($seller_ids as $seller_id =>  $bc_orders)
            {
                $sum = round(array_sum(array_column($bc_orders, 'total_order_cost')),2) ;
                $date_sums[$date][$seller_id]    = $sum;
                $sellers[$seller_id]  = 1;
                $total[] = $sum;
            }
        }
    
        $sellers  =   array_keys($sellers);
        $seller_names = DB::table('sellers')
            ->whereIn('id',$sellers)
            ->pluck('name','id')
            ->toArray();
        $total = array_sum($total);
        
        /*FINDING EMPTY GAPS IN TIME PERIODS, FOR EXAMPLE IF WE CREATED NEW COMPANY LATER IN
     COUPLE OF MONTHS, NEW COMPANY WOULDN'T HAVE DATA FROM PREVIOUS  PERIODS AS OLDER COMPANY WOULD HAVE
      */
        $seller_data = self::fill_the_gaps($date_sums,$sellers);
        
        foreach($seller_data as $seller_id =>    $data)
        {
            $period_sellers[$seller_names[$seller_id]]    =   array_sum($data);
            $color = self::random_color();
            $percentage_colors[$seller_names[$seller_id]] = $color;
            $datasets[] =
                [
                    'data'=> $data,
                    'label'=> $seller_names[$seller_id],
                    'borderColor'=> $color,
                    'fill'=> false
                ];
        }
        arsort($period_sellers);
      
        $percentage = self::labels($period_sellers,$total)['percentage'];
        
        return self::by_months_graph(
            array_values($datasets),
            $labels,
            $colors,
            'line',
            self::graph_title($year,$top_products,$period)[__FUNCTION__],
            $percentage,
            $percentage_colors,
            true);
    }
    static function by_product_periods($guard,$opposite_guard,$company,$year,$top_products,$period)
    {
        
        $orders = self::period_orders($guard,$year,$period) ;
        $labels = array_keys($orders);
        $products   =   [];
        $product_sales =[];
        $datasets = [];
        $colors = [];
        $yearly = [];
        $total = [];
        $products_total = [];
        $summed_products=[];
        
        foreach($orders as $date    =>  $data)
        {
            foreach($data as $order)
            {
                $products[$date][]  =  json_decode($order->order,true)  ;
            }
            foreach($products[$date]   as  $date_orders)
            {
                foreach($date_orders as $product_name => $data)
                {
                    /*adding total_product_price on 0 index otherwise $product_sales[$product_name] would accumulate during the loop*/
                    $product_sales[$product_name][0] = $data['total_product_price'];
                    $products_total[$product_name][] = $data['total_product_price'];
                    /*and we would get not correct results, here we are summing straight away and storing values
					in $counted products*/
                    $counted_products[$date][$product_name] [] = array_sum($product_sales[$product_name]) ;
                    /*and subsequently in $summed_products*/
                    $summed_products[$product_name][$date] = round(array_sum($counted_products[$date][$product_name]) ,2);
                }
            }
        }
        
        foreach ($products_total as $product =>  $sums)
        {
            $yearly[$product] = array_sum($sums);
            $total[] =    array_sum($sums);
        }
        arsort($yearly);
        
        /* getting top  selected products only*/
        $sliced = array_slice($yearly, 0, $top_products);
        
        /*removing sliced out  products from summed products*/
        foreach( array_diff($yearly,$sliced) as $index_to_remove =>  $data)
        {
            unset($summed_products[$index_to_remove]);
        }
        
        $total = array_sum($total);
        $filled_products = self::fill_the_gaps($summed_products,$labels,true);
        
        foreach($filled_products as $product     => $data)
        {
            $color = self::random_color();
            $percentage_colors [$product] = $color;
            $datasets[] =
                [
                    'data'=>  array_values($data),
                    'backgroundColor'   => $color,
                    'label' =>  $product
                ];
        }
        
        $percentage = self::labels($sliced,$total)['percentage'];
        
        return self::by_months_graph(
            array_values($datasets),
            $labels,
            $colors,
            'bar',
            self::graph_title($year,$top_products,$period)[__FUNCTION__],
            $percentage,
            $percentage_colors,
            true);
    }
    /*CURRENT MONTH*/
    /*HELPERS...*/
    static function orders($guard,$year,$group_by = null)
    {
        return  $group_by ?
            StatsRepository::orders($guard,$year)->groupBy($group_by)->toArray()
            :
            StatsRepository::orders($guard,$year)->toArray();
    }
    static function period_orders($guard,$year,$period,$group_by = null)
    {
    /*current month of current year*/
        /*$period is acting strangely , not working when applying directly .....*/
      if($period == 'd')
      {
          return  $group_by ?
              StatsRepository::orders($guard,date('Y'),$period)
                  ->groupBy([function($val) {
                      return Carbon::parse($val->created_at)->format('d');
                  },$group_by])->toArray()
              :
              StatsRepository::orders($guard,date('Y'),$period)
                  ->groupBy([function($val) {
                      return Carbon::parse($val->created_at)->format('d');
                  }])->toArray();
      }
        /*selected year by month*/
        return  $group_by ?
            StatsRepository::orders($guard,$year,$period)
                ->groupBy([function($val,$period) {
                    return Carbon::parse($val->created_at)->format('M');
                },$group_by])->toArray()
            :
            StatsRepository::orders($guard,$year,$period)
                ->groupBy([function($val,$period) {
                    return Carbon::parse($val->created_at)->format('M');
                }])->toArray();
       
        
    }
    static function current_month_orders($guard,$year,$group_by = null)
    {
        return  $group_by ?
            StatsRepository::orders($guard,$year)
                ->sortBy('created_at')
                ->groupBy([function($val) {
                    return Carbon::parse($val->created_at)->format('M');
                },$group_by])->toArray()
            :
            StatsRepository::orders($guard,$year)
                ->sortBy('created_at')
                ->groupBy([function($val) {
                    return Carbon::parse($val->created_at)->format('M');
                }])->toArray();
        
    }
    static function graph_title($year,$top_products,$period = null)
   {
       
       $titles = [
           'by_departments'          => $year.' '. __('sales by departments.'),
           'by_locations'            => $year.' '. __('sales by locations.'),
           'by_companies'            => $year.' '. __('sales by companies.'),
           'by_products'             => $year.' '. __('sales by products:').' '.__('top').' '.$top_products,
           'by_sellers'              => $year.' '. __('sales by sellers.'),
           'by_department_periods'   => $period == 'd'  ? date('F').' '.date('Y').' '. __('sales by department.')
               :  $year.' '. __('sales by department.'),
           'by_location_periods'     => $period == 'd'  ? date('F').' '.date('Y').' '. __('sales by locations.')
               :  $year.' '. __('sales by locations.'),
           'by_company_periods'      => $period == 'd'  ? date('F').' '.date('Y').' '.__('sales by companies.')
               :  $year.' '. __('sales by companies.'),
           'by_product_periods'      => $period == 'd'  ? date('F').' '.date('Y').' '. __('sales by products:')
               .' '.__('top').' '.$top_products  :  $year.' '. __('sales by products:') .' '.__('top').' '.$top_products,
           'by_seller_periods'       => $period == 'd'  ? date('F').' '.date('Y').' '. __('sales by sellers.')
               :  $year.' '. __('sales by sellers.'),
       ];
       
       return $titles;
      
   }
   static function max_graph()
    {
        return 20;
    }
    static function break_point()
    {
        return 10;
    }
    static function random_color()
    {
        return '#'.substr(str_shuffle('ABCDEF0123456789'), 0, 6);
    }
    static function fill_the_gaps($with_gaps,$full,$products = null)
    {
        
        $gaps_filled = [];
       
        /*GROUPED BAR GRAPH*/
        if($products)
        {
            foreach($with_gaps as $product => $p_with_gaps)
            {
                $product_filled[$product] = array_keys($p_with_gaps);
               
                  foreach($full as $full_month)
                  {
                        if(in_array($full_month,$product_filled[$product]))
                        {
                            $gaps_filled[$product] [$full_month]= $p_with_gaps[$full_month];
                        }
                        else
                        {
                            $gaps_filled[$product] [$full_month]= '';
                        }
                  }
             }
          
          
        }
        else
        {
            foreach($with_gaps as $date  =>    $sums)
            {
                $gaps[$date] = array_diff($full,array_keys($sums));
        
                foreach($sums as $name    =>  $sum)
                {
            
                    !is_array($sum) ? :$sum = array_sum($sum) ;
            
                    $gaps_filled[$name] []= $sum;
                }
                if($gaps[$date] != [])
                {
                    foreach ($gaps[$date] as $inx => $missing_name)
                    {
                        $gaps_filled[$missing_name] []= '';
                    }
                }
            }
        }
       
       // dd($gaps_filled);
        return $gaps_filled;
    }
    static function type($data)
    {
        return  sizeof($data) >= self::break_point() ? 'bar':'doughnut';
    }
    static function labels($data,$total)
    {
        $labels = [];
        $percentage = [];
        foreach($data as $item => $sum)
        {
            $labels[]   = $item;
            $percentage[str_replace('_',' ',$item)] = $sum .' ( '.round($sum / $total * 100,1).' % )' ;
        }
        
        return [ 'labels'   => array_splice($labels,0,self::max_graph()) , 'percentage' =>    $percentage] ;
    }
    static function by_months_graph($datasets,$labels,$colors,$type,$title,$percentage,$percentage_colors,$display_legend = false)
    {
        return
            [
                'percentage'    =>      $percentage,
                'percentage_colors'    =>      $percentage_colors,
                'type'          =>      [$type] ,
                'data'          =>      [
                    'datasets'   =>      array_splice($datasets,0,self::max_graph()),
                    'labels'        =>   array_splice($labels,0,self::max_graph()),
                ],
                'options'       =>
                    [
                        'legend' => [
                            'display'    =>  $display_legend,
                            'position'   =>  'bottom',
                            'labels'     => [
                                'fontColor' => '#356FCD',
                            ]
                        ],
                        'title' => [
                            'display'     => true,
                            'fontSize'    => 20,
                            'text'        =>  str_replace(' ', '_',__($title))
                        ],
                        'tooltips' => [
                            
                            'titleFontSize'      => 0,
                        
                        ],
                        'elements'    =>  [
                            'arc'  =>  [
                                'backgroundColor'   =>  $colors
                            
                            ]
                        ]
                        
                    ]
            ];
    }
    static function yearly_graph($by_data,$colors,$function_name,$year,$top_products=null)
    {
        arsort($by_data);
        $total  =   array_sum($by_data);
        $title = self::graph_title($year,$top_products)[$function_name];
        $type = self::type($by_data);
        $labels = self::labels($by_data,$total)['labels'];
        $percentage = self::labels($by_data,$total)['percentage'];
        $colors = array_splice($colors,0,sizeof($percentage));
        
        $percentage_colors = array_combine(array_keys($percentage),$colors);
        $colors = array_splice($colors,0,self::max_graph());
       
        $display_legend =  sizeof($by_data) >= self::break_point() ? false:true;
        $datasets  [] =   [
            'data'    =>  array_values( array_splice($by_data,0,self::max_graph())),
            'backgroundColor'   =>   $colors,
    
        ];
       
      
        return
            [
                'percentage'    =>      $percentage,
                'percentage_colors'    =>      $percentage_colors,
                'type'          =>      [$type] ,
                'data'          =>      [
                    'datasets'   => array_values($datasets),
                    'labels'        =>      $labels,
                ],
                'options'       =>
                    [
                        'legend' => [
                            'display'    =>  $display_legend,
                            'position'   =>  'bottom',
                            'labels'     => [
                                'fontColor' => '#356FCD',
                            ]
                        ],
                        'title' => [
                            'display'     => true,
                            'fontSize'    => 20,
                            'text'        =>  str_replace(' ', '_',__($title))
                        ],
                        'tooltips' => [
                            
                            'titleFontSize'      => 0,
                        
                        ],
                        'elements'    =>  [
                            'arc'  =>  [
                                'backgroundColor'   =>  $colors
                            
                            ]
                        ]
                    
                    ]
            ];
    }
    
}
