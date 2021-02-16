<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 03-Oct-19
 * Time: 22:28
 */

namespace App\Services;


use Illuminate\Support\Facades\DB;


class ProductList
{
   
    
    static function change_preferred_language($from,$to,$department = null)
    {
       
        if(isset($department))
        {
            $product_lists = DB::table('product_lists')
                ->where('buyer_company_id',session()->get('company_id'))
                ->where('department',$department)
                ->pluck('product_list','language')
                ->toArray();
            
            foreach($product_lists as $language   =>  $list)
            {
                $product_lists_[$language]   =   json_decode($list,true);
            }
            $new_preferred_product_list = session()->pull('new_preferred_product_list');
            
            $translator = array_combine(array_values($product_lists_[$from]) , array_values($new_preferred_product_list)[0] );
            
            foreach($product_lists_ as $language    =>  $list)
            {
                if($language == $from)
                {
                    $translated_product_list[$from]  =   array_flip($translator);
                }
                else{
                    foreach($list as $product=>$translation)
                    {
        
                        $translated_product_list[$language][$translator[$product]]    =   $translation;
                    }
                }
            }
    
            $translated_product_list[$to]   =   $new_preferred_product_list[$to];
            
        // dd($translated_product_list);
    
            if(isset($translated_product_list))
            {
                foreach($translated_product_list as $language => $product_list)
                {
                  
                    DB::table('product_lists')
                        ->updateOrInsert(
                            ['language'=> $language,'department' => $department, 'buyer_id' => Role::get_owner_id_s(), 'buyer_company_id' => session()->get('company_id')],
                            ['product_list' =>  json_encode($product_list),'updated_at'=>date('Y-m-d H:i:s'),'created_at'=>date('Y-m-d H:i:s')]);
                    
                }
            }
            return true;
        }
        else
        {
            $product_lists = DB::table('product_lists')
                ->where('buyer_company_id',session()->get('company_id'))
                ->get(['product_list','department','language'])
                ->toArray();

            $new_product_list=    [];
            foreach($product_lists as $data)
            {
                $new_product_list[$data->department][$data->language]  =  json_decode($data->product_list,true);
                
            }
     

            foreach($new_product_list  as $department   => $new_lists)
            {

                if(isset($new_lists[$to]))
                {
                    $translator = $new_lists[$to];
                    foreach($new_lists as $language => $product_list)
                    {
                        if($language == $from)
                        {
                            $translated_product_list[$department][$from]  =   array_flip($new_lists[$to]);
                        }
                        elseif($language == $to)
                        {
                            $translated_product_list[$department][$to]  =  array_unique(     array_values($new_lists[$to])   ) ;
                        }
                        else{
                            foreach($product_list as $product=>$translation)
                            {

                                $translated_product_list[$department][$language][$translator[$product]]    =   $translation;
                            }
                        }

                    }

                }
            }

            if(isset($translated_product_list))
            {
                foreach($translated_product_list as $department => $product_lists)
                {
                    foreach($product_lists as $language =>  $product_list)
                    {
                        DB::table('product_lists')
                            ->where('buyer_company_id',session()->get('company_id'))
                            ->where('department',$department)
                            ->where('language',$language)
                            ->update([
                                'product_list'   =>  json_encode($product_list) ,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                    }
                   
                }
            }




            return true;
        }
       
    }
    
    static function disable_language(array $languages)
    {
        if($languages   !=  [])
        {
            foreach($languages as $language)
            {
                DB::table('product_lists')
                    ->where('buyer_company_id',session()->get('company_id'))
                    ->where('language',$language)
                    ->update([
                        'disabled'  =>  1,
                        'updated_at'    =>  date('Y-m-d H:i:s')
                    ]);
            }
        }
        
    }
    
    static function enable_language(array $languages)
    {
        if($languages   !=  [])
        {
            foreach($languages as $language)
            {
                DB::table('product_lists')
                    ->where('buyer_company_id',session()->get('company_id'))
                    ->where('language',$language)
                    ->update([
                        'disabled'  =>  0,
                        'updated_at'    =>  date('Y-m-d H:i:s')
                    ]);
            }
        }
    }
    
}
