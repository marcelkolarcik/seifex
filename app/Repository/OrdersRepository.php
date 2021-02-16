<?php

namespace App\Repository;

use App\Services\LocationNameOrId;
use DB;
use App\Services\Company;
use App\Services\Role;

class OrdersRepository{

 public $company;
 public $role;
 
 public function __construct(Company $company, Role $role)
 {
     $this->company =   $company;
     $this->role =   $role;
 }
    
    private function companies()
    {
        return $this->company->for($this->role->get_owner_or_staff());
    }
    public function orders()
    {
        
      
        
        //// here if you are seller, we will sor the orders by locations
        
        $ords    =   [];
        $locations_staff_works_with = [];
        $bc_ids =   [];
        if($this->role->company_id() ==  'seller_company_id')
        {
            $orders_query  =    DB::table('orders')
                ->join('buyer_companies','orders.buyer_company_id','=','buyer_companies.id')
                ->where('seller_company_id',session()->get('company_id'));
            
            
            /*IF IT IS EMPLOYEE WITH POSITION STAFF WE'LL CHECK FOR LOCATIONS HE IS WORKING WITH*/
            $logged_in_staff = $this->companies()[session()->get('company_id')]->logged_in_staff;
            
            if($logged_in_staff)
            {
                /*seller_seller, seller_delivery, buyer_buyer*/
                if(isset($logged_in_staff['scope']['departments']))
                {
                    /*STAFF SCOPE DEPARTMENTS*/
                    $scope_departments    =   $logged_in_staff['scope']['departments'];
                    /*COMPANY PRICE LISTS DEPARTMENTS*/
                //  $this->company->update('price_lists');
                  
                    $pl_departments =   array_keys($this->companies()[session()->get('company_id')]->price_lists);
                    $departments    =    array_intersect($scope_departments,$pl_departments);
    
                    /*IF DEPARTMENTS FOR STAFF IS NOT EMPTY WE'LL USE IT, OTHERWISE WE'LL USE ALL DEPARTMENTS*/
                    if($departments != [])
                    {
                        $orders_query->whereIn('department', $departments);
                    }
                }
              
                /*buyer_accountant, seller_accountant*/
                if( isset($logged_in_staff['scope']['companies']) )
                {
                    $bc_ids =   $logged_in_staff['scope']['companies'];
                   
                }
                /*seller_seller, seller_delivery*/
                elseif(isset($logged_in_staff['scope']['base_locations']))
                {
                    !isset($logged_in_staff['scope']['base_locations']['country']) ?
                        : $locations_staff_works_with[]['country'] = array_keys($logged_in_staff['scope']['base_locations']['country']) ;
    
                    !isset($logged_in_staff['scope']['base_locations']['county']) ?
                        : $locations_staff_works_with[]['county'] = array_keys($logged_in_staff['scope']['base_locations']['county']) ;
    
                    !isset($logged_in_staff['scope']['base_locations']['county_l4']) ?
                        : $locations_staff_works_with[]['county_l4'] = array_keys($logged_in_staff['scope']['base_locations']['county_l4']) ;
    
    
                    $staff_works_with=DB::table('buyer_companies');
    
                    foreach($locations_staff_works_with as $key => $wheres)
                    {
                        foreach($wheres as $level   => $ids)
                        {
                            if($key ==  0)
                            {
                                $staff_works_with->whereIn($level, $ids);
                            }
                            else
                            {
                                $staff_works_with->orWhereIn($level, $ids);
                            }
                        }
                    }
                    $bc_ids =    $staff_works_with->pluck('id')->toArray();
                }
                if($bc_ids == []) return [];
                if($bc_ids != [])  $orders_query->whereIn('orders.buyer_company_id',$bc_ids);
    
                $orders =     $orders_query->get(['orders.*','buyer_companies.county_l4','buyer_companies.county','buyer_companies.country'])
                    ->sortByDesc('created_at')
                    ->groupBy(['department','county_l4'])
                    ->toArray();
                
                if($orders == []) return [];
                
            }
            /*END OF IF IT IS EMPLOYEE WITH POSITION STAFF WE'LL CHECK FOR LOCATIONS HE IS WORKING WITH*/
            
            /*OWNER*/
            else
            {
                $orders =     $orders_query->get(['orders.*','buyer_companies.county_l4','buyer_companies.county','buyer_companies.country'])
                    ->sortByDesc('created_at')
                    ->groupBy(['department','county_l4'])
                    ->toArray();
    
                if($orders == []) return [];
            }
            
            foreach($orders as $department   =>  $location_orders)
            {
                foreach ($location_orders as $location   =>  $dept_orders)
                {
                    foreach($dept_orders as $order)
                    {
                        if($order->buyer_confirmed_delivery_at != null)
                        {
                            $sorted_orders[$department][LocationNameOrId::get_name($location).' ('.str_replace('--',' | ',LocationNameOrId::get_name($order->country)).')']['received'][]
                                =   $order;
                            
                            if($order->comment != null)
                            {
                                $sorted_orders[$department][LocationNameOrId::get_name($location).' ('.str_replace('--',' | ',LocationNameOrId::get_name($order->country)).')']['received_commented'][]
                                    =   $order;
                                
                            }
                            
                        }
                        elseif($order->delivered_at != null)
                        {
                            $sorted_orders[$department][LocationNameOrId::get_name($location).' ('.str_replace('--',' | ',LocationNameOrId::get_name($order->country)).')']['delivered'][]
                                =   $order;
                        }
                        elseif($order->prepped_at != null)
                        {
                            $sorted_orders[$department][LocationNameOrId::get_name($location).' ('.str_replace('--',' | ',LocationNameOrId::get_name($order->country)).')']['dispatched'][]
                                =   $order;
                        }
                        elseif($order->checked_at != null)
                        {
                            $sorted_orders[$department][LocationNameOrId::get_name($location).' ('.str_replace('--',' | ',LocationNameOrId::get_name($order->country)).')']['seen'][]
                                =   $order;
                        }
                        elseif($order->checked_at == null)
                        {
                            $sorted_orders[$department][LocationNameOrId::get_name($location).' ('.str_replace('--',' | ',LocationNameOrId::get_name($order->country)).')']['new'][]
                                =   $order;
                        }
                    }
                }
            }
            
            
            foreach($sorted_orders as $department=>$locations)
            {
                foreach($locations as $location=>$types)
                {
                    // dd($types['new']);
                    
                    
                    if(isset($types['received']))
                    {
                        $ords[$department][$location]['received']   =   $types['received'];
                    }
                    if(isset($types['delivered']))
                    {
                        $ords[$department][$location]['delivered']   =   $types['delivered'];
                    }
                    if(isset($types['dispatched']))
                    {
                        $ords[$department][$location]['dispatched']   =   $types['dispatched'];
                    }
                    if(isset($types['seen']))
                    {
                        $ords[$department][$location]['seen']   =   $types['seen'];
                    }
                    if(isset($types['new']))
                    {
                        $ords[$department][$location]['new']   =   $types['new'];
                    }
                    if(isset($types['received_commented']))
                    {
                        $ords[$department][$location]['received_commented']   =   $types['received_commented'];
                    }
                }
                
            }
            
            return $ords;
        }
        //// BUYER
        elseif($this->role->company_id() ==  'buyer_company_id')
        {
            $logged_in_staff = $this->companies()[session()->get('company_id')]->logged_in_staff;
            $orders_query =    DB::table('orders')
                ->where('buyer_company_id',session()->get('company_id'))
                ->get();
            if($logged_in_staff)
            {
           /*BUYER*/
            if( isset($logged_in_staff['scope']['departments']) && $logged_in_staff['role'] == 'buyer_buyer')
            {
              
                /*STAFF SCOPE DEPARTMENTS*/
                $scope_departments    =   $logged_in_staff['scope']['departments'];
                /*COMPANY PRICE LISTS DEPARTMENTS*/
                //  $this->company->update('price_lists');
        
                $pl_departments =   array_keys($this->companies()[session()->get('company_id')]->price_lists);
                $departments    =    array_intersect($scope_departments,$pl_departments);
        
                /*IF DEPARTMENTS FOR STAFF IS NOT EMPTY WE'LL USE IT, OTHERWISE WE'LL USE ALL DEPARTMENTS*/
                if($departments != [])
                {
                    $orders_query->whereIn('department', $departments);
                }
            }
            elseif( !isset($logged_in_staff['scope']['departments']) && $logged_in_staff['role'] == 'buyer_buyer')
            {
                return [];
            }
            /*ACCOUNTANT*/
            if( isset($logged_in_staff['scope']['companies']) && $logged_in_staff['role'] == 'buyer_accountant')
            {
               
                $orders_query->whereIn('seller_company_id', $logged_in_staff['scope']['companies']);
                
            }
            elseif(!isset($logged_in_staff['scope']['companies']) && $logged_in_staff['role'] == 'buyer_accountant')
            {
                return [];
            }
           
            $orders = $orders_query
                ->sortByDesc('created_at')
                ->groupBy('department')
                ->toArray();
            }
            else
            {
                $orders = $orders_query
                    ->sortByDesc('created_at')
                    ->groupBy('department')
                    ->toArray();
            }
           
            if($orders == []) return [];
            $sorted_orders   =   [];
            
            
            foreach($orders as $department   =>  $d_orders)
            {
                foreach($d_orders as $order)
                {
                    if($order->buyer_confirmed_delivery_at != null)
                    {
                        $sorted_orders[$department]['received'][]   =   $order;
                        
                        if($order->comment != null)
                        {
                            $sorted_orders[$department]['received_commented'][]   =   $order;
                            
                        }
                        
                    }
                    elseif($order->delivered_at != null)
                    {
                        $sorted_orders[$department]['delivered'][]   =   $order;
                    }
                    elseif($order->prepped_at != null)
                    {
                        $sorted_orders[$department]['dispatched'][]   =   $order;
                    }
                    elseif($order->checked_at != null)
                    {
                        $sorted_orders[$department]['seen'][]   =   $order;
                    }
                    elseif($order->checked_at == null)
                    {
                        $sorted_orders[$department]['new'][]   =   $order;
                    }
                }
            }
            
            foreach($sorted_orders as $department=>$department_orders)
            {
                
                
                
                if(isset($department_orders['received']))
                {
                    $ords[$department]['received']   =   $department_orders['received'];
                }
                if(isset($department_orders['delivered']))
                {
                    $ords[$department]['delivered']   =   $department_orders['delivered'];
                }
                if(isset($department_orders['dispatched']))
                {
                    $ords[$department]['dispatched']   =   $department_orders['dispatched'];
                }
                if(isset($department_orders['seen']))
                {
                    $ords[$department]['seen']   =   $department_orders['seen'];
                }
                if(isset($department_orders['new']))
                {
                    $ords[$department]['new']   =   $department_orders['new'];
                }
                if(isset($department_orders['received_commented']))
                {
                    $ords[$department]['received_commented']   =   $department_orders['received_commented'];
                }
            }
            
            return $ords;
        }
        
    }
    public function record_orders($order,$seller_company_id,$buyer_company_id,$department,$total_order_cost)
	{
  
		$current_order = [];
        $seifex_order = []; //// no hash with the name
		
		foreach($order as $product_name => $product_data)
		{
			if(isset($product_data['box_size']))
			{
				$order_value = floatval($product_data['box_size']) * floatval($product_data['amount']) * floatval($product_data['price_per_kg']);
                $order_amount = floatval($product_data['box_size']) * floatval($product_data['amount']) ;
			}
			else
			{
				$order_value = floatval($product_data['amount']) * floatval($product_data['price_per_kg']);
                $order_amount =  floatval($product_data['amount']) ;
			}
			
			$current_order[trim(explode('+',$product_name)[0])] = [
				'order_value'   =>  $order_value,
				'amount'        =>  $order_amount
			];
			
            
            $seifex_order[trim(explode('+',$product_name)[0])]  =   $product_data;
		}
       
        $seller_location  = $this->seller_location($buyer_company_id,$seller_company_id);
        
        $buyer_location = $this->buyer_location($buyer_company_id);
       
       
        ///  BUYER/SELLER STATISTICS
		$this->purchase_sales_statistics($current_order, $buyer_company_id, $seller_company_id, $department,$total_order_cost );
		
		//// ADMIN  STATISTICS
        $this->country_purchase_sales_statistics( $buyer_location, $seller_location,  $department , $total_order_cost);
        
        //////  SEIFEX INCOME STATISTICS
        
        $this->seifex_statistics( $seller_company_id,$buyer_company_id,$department,$buyer_location,$seller_location,$seifex_order,$total_order_cost);
        
		return 'ok';
		
	}
	
	private function buyer_location($buyer_company_id)
    {
        return $this->companies()[$buyer_company_id];
    }
    private function seller_location($buyer_company_id,$seller_company_id)
    {
        return $this->companies()[$buyer_company_id]->seller_companies[$seller_company_id];
    }
    private function seifex_statistics($seller_company_id,$buyer_company_id,$department,$buyer_location,$seller_location,$order,$total_order_cost)
    {
        DB::table('seifex_orders')
            ->insert([
                'seller_company_id' => $seller_company_id,
                'buyer_company_id'  => $buyer_company_id,
                'department'        => $department,
                'buyer_country'     => $buyer_location->country,
                'buyer_county'      => $buyer_location->county,
                'buyer_county_l4'   => $buyer_location->county_l4,
                'seller_country'    => $seller_location['country'],
                'seller_county'     => $seller_location['county'],
                'seller_county_l4'  => $seller_location['county_l4'],
                'order'             =>  json_encode($order),
                'order_value'       => $total_order_cost,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s')
            ]);
    }
	private function purchase_sales_statistics($current_order,$buyer_company_id, $seller_company_id, $department,$total_order_cost)
	{
	 
		$seller_product_list_new = [];
		$seller_product_list_old= [];
		
		if(!$product_list =
			json_decode(DB::table('purchase_sales_statistics')
			->where('buyer_company_id', $buyer_company_id)
            ->where('seller_company_id', $seller_company_id)
			->where('department', $department)
			->pluck('product_list')
			->first(),true))
		{
			DB::table('purchase_sales_statistics')
				->insert([
                    'buyer_company_id'          =>  $buyer_company_id,
                    'seller_company_id'         =>  $seller_company_id,
					'department'                =>  $department,
					'product_list'              =>  json_encode($current_order),
                    'order_value'               =>  $total_order_cost,
                    'created_at'                => date('Y-m-d H:i:s'),
				]);
		}
		else
		{
			///// compare $buyer_product_list with $current_order and add new or update old records
			
			$new_products = array_diff(array_keys($current_order),array_keys($product_list));
			$old_products_1 = array_diff(array_keys($current_order),$new_products);
			$old_products_2 = array_diff(array_keys($product_list),array_keys($current_order));
			$old_products = array_merge($old_products_1,$old_products_2);
			
			foreach($new_products as $new_product)
			{
				$seller_product_list_new[$new_product]['order_value']  =  $current_order[$new_product]['order_value'];
				$seller_product_list_new[$new_product]['amount']  = $current_order[$new_product]['amount'];
			}
			foreach($old_products as $old_product)
			{
				if(in_array($old_product,array_keys($current_order)))
				{
					$seller_product_list_old[$old_product]['order_value']  = floatval($product_list[$old_product]['order_value'])  + floatval($current_order[$old_product]['order_value']);
					$seller_product_list_old[$old_product]['amount']  = floatval($product_list[$old_product]['amount'])  + floatval($current_order[$old_product]['amount']);
				}
				else
				{
					$seller_product_list_old[$old_product]['order_value']  = floatval($product_list[$old_product]['order_value'])  ;
					$seller_product_list_old[$old_product]['amount']  = floatval($product_list[$old_product]['amount']) ;
				}
				
			}
			
			$_product_list = array_merge($seller_product_list_new,	$seller_product_list_old);
           
                
                DB::table('purchase_sales_statistics')
                    ->where('buyer_company_id', $buyer_company_id)
                    ->where('seller_company_id', $seller_company_id)
                    ->where('department', $department)
                    ->update([
                        'product_list' => json_encode($_product_list),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'order_value' => DB::raw('order_value+' . $total_order_cost),
                    ]);
          
			
		}
	}
	private function country_purchase_sales_statistics($buyer_location, $seller_location, $department , $total_order_cost)
    {
      
        if(!DB::table('country_purchase_sales_statistics')
            ->where('buyer_country', $buyer_location->country)
            ->where('seller_country', $seller_location['country'])
            ->where('department', $department)
            ->update([
                'order_value'   =>  DB::raw('order_value+'.$total_order_cost),
                'updated_at'    =>  date('Y-m-d H:i:s'),
            ]))
            
        DB::table('country_purchase_sales_statistics')->insert([
            'department'            =>  $department,
            'buyer_country'         =>  $buyer_location->country,
            'seller_country'        =>  $seller_location['country'],
            'order_value'           =>  $total_order_cost,
            'created_at'            =>  date('Y-m-d H:i:s'),
            'updated_at'            =>  date('Y-m-d H:i:s'),
        ]);
    }
   
   
}
