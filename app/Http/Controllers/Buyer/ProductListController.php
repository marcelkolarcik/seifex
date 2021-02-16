<?php

namespace App\Http\Controllers\Buyer;

use App\Events\SellerNotificationEvent;
use App\Services\Language;
use App\Services\ProductList;
use App\Services\Role;
use App\Services\Sanitizer;
use App\Services\StrReplace;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Services\Company;

class ProductListController extends Controller
{
    public $company;
    public $role;
   
    public function __construct(  Company $company, Role $role)
    {
        
        $this->company                  =   $company;
        $this->role                     =   $role;
        $this->middleware('buyer.auth:buyer');
    }
    
  
    private function companies()
   {
      return  $this->company->for(   $this->role->get_owner_or_staff());
   }
   
    public function index(Request $request,$department = null)
    {
        if($request->ajax())
        {
         
            $product_list  = isset($this->product_list()[str_replace(' ','_',$request->selected_department)]) ?
                $this->product_list()[str_replace(' ','_',$request->selected_department)]
                : [] ;
            
            if($product_list === [] && $request->selected_lang  ===  session()->get('company_preferred_lan'))
                return 'preferred_empty';
            if($product_list === [])
                return 'empty';
            if($request->selected_lang  ===  session()->get('company_preferred_lan'))
                return 'preferred';
        }
        
        $action = 'create';
        
        $company    =   $this->companies()[ session()->get('company_id') ];
        
        $languages  =Language::get_language_names($company->languages);
        
        $default_departments = DB::table('default_departments')->pluck('department')->toArray();
        
        foreach($default_departments as $dep)
        {
            $translated_dep[]   =   __(str_replace('_',' ',$dep));
        }
        
        $default_departments = array_combine($default_departments, $translated_dep);
       
        //$default_departments = array_combine($default_departments, $default_departments);
        
        //// GET REQUEST FROM URL , WHEN USER CLICKS ON BACK BUTTON ON CHECK PAGE
        $product_list = '';
//        //dd($department);
//        if($department)
//        {
//            dd($this->product_list()[$department]);
//            $product_list = implode(',',json_decode( $this->product_list()[$department],true));
//        }
        
        
        $saved = false;
        
        return view('buyer.product_list.create',compact(
            'action',
            
            'saved',
            'default_departments',
            'product_list',
            'department','company','languages'));
    }
    public function check(Request $request)
    {
        
        $buyer_company  = $this->companies() [session()->get('company_id') ];
        
        
        $saved          = false;
        $action         = 'check';
        
        
        $language       = $request->language;
        $department     = $request->department;
        $preferred_lang =  Language::get_language_names([session()->get('company_preferred_lan')],'short_long') ;
        $selected_lang  =  Language::get_language_names([$request->language],'short_long') ;
        $products_trimmed = [];
        $new_products   =   [];
        $request->new_products == null ?:
            $new_products   = Sanitizer::do_strings(explode(',',$request->new_products));
        $new_product_list = [];
        $product_list = [];
        $product_key        =   'key';
        $translator_language = session()->get('translator_language');
        $preferred_changed = session()->get('preferred_changed');
        //// BUYER IS ADDING PRODUCTS FOR THE FIRST TIME
        if(!isset($request->product_list) && $request->new_products != null)
        {
            
            foreach($new_products as $product)
            {
                e($product) == '' ?:
                    $products_trimmed[] =  e($product);
            }
            $new_product_list = array_unique($products_trimmed);
            
            $product_list [$preferred_lang['short']] = $new_product_list;
            
            return view('buyer.product_list.check', compact(
                'action',
                'saved',
                'department',
                'product_list',
                'buyer_company',
                'language',
                'preferred_lang',
                'selected_lang',
                'product_key',
                'preferred_changed'));
        }
        //// IF IT IS COMPANY PREFERRED LANGUAGE WE WILL INSERT PRODUCTS TO
        /// COMPANY PREFERRED LANGUAGE ARRAY ONLY
        elseif(isset($request->product_list) &&  sizeof($request->product_list) == 1)
        {
            /*BUYER IS CHANGING PREFERRED LANGUAGE AND HAS NO PRODUCT TRANSLATION YET*/
            if(isset($request->product_list_translator))
            {
                foreach($new_products as $product)
                {
//                    trim($product) == '' ?:
                    $products_trimmed[] =  e($product);
                }
                $new_product_list = array_unique($products_trimmed);
                
                
                $product_list_  =  array_unique( array_merge($request->product_list[$language],$new_product_list));
                
                
                $product_list [$preferred_lang['short']] = array_combine($product_list_,array_values($request->product_list_translator)[0]) ;
                
            }
            else
            {
                foreach($new_products as $product)
                {
//                    trim($product) == '' ?:
                    $products_trimmed[] =  e($product);
                }
                $new_product_list = array_unique($products_trimmed);
                
                
                $product_list_  =  array_unique( array_merge($request->product_list[$language],$new_product_list));
                
                $product_list [$preferred_lang['short']] = $product_list_;
            }
            
            
            
            //dd($preferred_lang,$selected_lang,$translator_language);
            return view('buyer.product_list.check', compact(
                'action',
                'saved',
                'department',
                'product_list',
                'buyer_company',
                'language',
                'preferred_lang',
                'selected_lang',
                'product_key',
                'preferred_changed',
                'translator_language'));
        }
        ///// WE HAVE TRANSLATION AND WE NEED TO STORE TRANSLATION INTO TRANSLATION ARRAY.... MAYBE NOT
        elseif(isset($request->product_list) &&  sizeof($request->product_list) == 2)
        {
            $product_list[$selected_lang['short']] = $request->product_list[$selected_lang['short']];
            $product_list[$preferred_lang['short']] = $request->product_list[$preferred_lang['short']];
            
            //// IF THERE IS NO TRANSLATION RETURN BACK WITHOUT SAVING
            if(  array_filter($product_list[$selected_lang['short']],'strlen') == [] )
                return back()->with('no_translation','There was no '. __(StrReplace::currency_underscore($department)) .' translation to '.$selected_lang['long'].' !');
            
            $product_list = [];
            $product_list[$selected_lang['short']] =   array_combine(array_values($request->product_list[$preferred_lang['short']]),
                array_values($request->product_list[$selected_lang['short']]));
            
            return view('buyer.product_list.check', compact(
                'action',
                'saved',
                'department',
                'product_list',
                'buyer_company',
                'language',
                'preferred_lang',
                'selected_lang',
                'product_key',
                'preferred_changed'));
        }
        else
        {
            return back();
        }
        
    }
    public function show(Request $request)
    {
    
      
        /*buyer is changing department and / or language*/
        //// AJAX REQUEST
        if($request->ajax())
        {
    
            $selected_lang  =   null;
            $preferred_changed  =   false;
            $pl_translated      =   false;
            $pl_preferred       =   false;
           
            $preferred_lang     =  Language::get_language_names([session()->get('company_preferred_lan')],'short_long') ;
            if($request->language == null) return back();
          
            $selected_lang      =  Language::get_language_names([$request->language],'short_long') ;
         
        
            $preferred = false;
            if(session()->get('company_preferred_lan') === $request->lan ) $preferred = true;
            
    
    
    /*GETTING PRODUCT LIST FROM COMPANY DATA*/
            $product_list_  = isset(   $this->product_list()[$request->dep] ) ?
                $this->product_list()[$request->dep]  : [] ;
               
            
           
            /*BUYER IS CHANGING PREFERRED LANGUAGE AND HAS NO PRODUCT TRANSLATION YET*/
           if(!isset($product_list_[session()->get('company_preferred_lan')]) && !isset( $product_list_[$request->language]) && sizeof($product_list_) > 0)
            {
                $preferred_changed = true;
                $old_preferred_language_list    =   [];
                foreach($product_list_ as $language => $list)
                {
                    foreach(json_decode($list,true) as $key   => $product)
                    {
                
                        if($key  ===   0 )
                        {
                            $old_preferred_language_list[$language]  =  json_decode($list,true);
                            $translator_language = Language::get_language_names([$language],'short_long') ;
                            session()->put('translator_language',$translator_language);
                            break;
                        }
                        break;
                    }
                }
               
                $product_list = $old_preferred_language_list;
                $pl_preferred =   false;
       
            }
            elseif(!isset( $product_list_[$request->language]))
            {
              
                $product_key = 'key';
                $translation_needed=true;
                if(isset($product_list_[session()->get('company_preferred_lan')]))
                {
                    $product_list[session()->get('company_preferred_lan')] = json_decode($product_list_[session()->get('company_preferred_lan')],true);
                }
                else
                {
                    $product_list   =   null;
                }
             
               
            }
            elseif(isset( $product_list_[$request->language]) && $request->language!== session()->get('company_preferred_lan'))
            {
                $pl_translated = true;
                $product_key = 'product';
                $product_list[$request->language] =json_decode($product_list_[$request->language] ,true)  ;
              
            }
            elseif(isset( $product_list_[session()->get('company_preferred_lan')]) )
            {
                $pl_preferred =   true;
                $product_key = 'key';
                $product_list[session()->get('company_preferred_lan')] = json_decode($product_list_[session()->get('company_preferred_lan')],true);
               
            }
           
    
         
            session()->put('preferred_changed', $preferred_changed);
            return view('buyer.product_list.current', compact(
                'product_list',
                'pl_translated',
                'pl_preferred',
                'product_list',
                'preferred_lang',
                'selected_lang',
                'product_key',
                'preferred_changed',
                'translator_language'));
           
        }
    }
    public function delete(Request $request)
    {
        if($request->ajax())
        {
          
            DB::table('product_lists')
            ->where('department', '=',  str_replace(' ','_',$request->department))
            ->where('buyer_company_id', '=', $request->buyer_company_id )
            ->delete();
    
            DB::table('company_departments')
                ->where('buyer_company_id', '=', $request->buyer_company_id )
                ->where('department','=',str_replace(' ','_',$request->department))
                ->delete();
            
            $this->company->update('product_lists');
            
            return ['status'=>'deleted','text'=>$request->department.__(' was deleted !')];
        }
        
       
    }
    public function store(Request $request, $department)
    {
        $bc_id              = session('company_id');
        
        $new_product_list   =   $request->product_list[$request->language];
        $original_list      =   $request->product_list[session()->get('company_preferred_lan')];
       
        $preferred_lang     =  Language::get_language_names([session()->get('company_preferred_lan')],'short_long') ;
        $selected_lang      =  Language::get_language_names([$request->language],'short_long') ;
    
        $new_products       =   Sanitizer::do_strings($new_product_list);
        $original_products  =   Sanitizer::do_strings($original_list);
        
       
   
        /* IF ALL THE PRODUCTS ARE DELETED WE WILL DELETE DEPARTMENT */
        if(array_filter($new_product_list,'strlen') == [] && array_filter($original_list,'strlen') == [] )
        {
      
           
            DB::table('product_lists')
                ->where('department', '=', $department)
                ->where( 'buyer_id', $this->role->get_owner_id())
                ->where('buyer_company_id', $bc_id )
                ->delete();
        
            DB::table('company_departments')
                ->where('buyer_company_id','=',session('company_id'))
                ->where('department', '=', $department)
                ->delete();
    
    
    
            $this->company->update('product_lists');
            
            session()->flash('department_deleted',StrReplace::currency_underscore($department));
            return redirect('/product_list');
          
        }
      
        //// BUYER IS ADDING PRODUCTS IN HIS PREFERRED LANGUAGE
        elseif($request->language == session()->get('company_preferred_lan'))
        {
          
            $old_product_list = $this->old_product_list($department);
    
          
        
            ///WE HAVE PRODUCT LIST ALREADY SO WE NEED TO  UPDATE  LANGUAGE VERSIONS
            if($old_product_list != null )
            {
               
                session()->put('old_product_list',$old_product_list);
                $changes =   $this->check_for_changes($old_product_list,$new_products);
    
                $product_list  = $changes['product_list'];
                $changed_products   =   $changes['changed_products'];
              
                session()->flash('changed_products',$changed_products);
                session()->flash('changed_department',StrReplace::currency_underscore($department) );
                
            }
            ///WE DON'T HAVE PRODUCT LIST, SO WE NEED TO CREATE THIS LANGUAGE VERSION
            else
            {
                $product_list  =   $new_products;
              
                session()->flash('department_created',StrReplace::currency_underscore($department));
            }
  
            $this->update($department,$bc_id,$product_list,$request->language);
            
           
            
            DB::table('company_departments')
                ->updateOrInsert(
                    ['department' => $department,  'buyer_company_id' => $bc_id,'buyer_id' => $this->role->get_owner_id()],
                    ['department' =>  $department]
                );
            $this->company->update('product_lists');
            return redirect('/product_list');

        }
        //// BUYER IS ADDING TRANSLATION
        elseif($request->language != session()->get('company_preferred_lan'))
        {
           
            $changed_products=[];
            $product_list = $this->old_product_list($department);
          
            $translated_products_list   =   array_combine($original_products,$new_products);
           // dd($translated_products_list);
            $old_translated =   isset( $product_list[$request->language]) ? $product_list[$request->language] : [];
           
            $newly_translated   = array_diff(  array_values($translated_products_list), array_values( $old_translated));
          
            ///// FIRST TRANSLATION
            if($old_translated == []) $changed_products = $translated_products_list;
            else
             {
                  foreach ($newly_translated as $key  => $product)
                  {
                      $changed_products[array_flip($translated_products_list)[$product]]  =
                          $translated_products_list[array_flip($translated_products_list)[$product]]  ;
                  }
             }
    
           
            $product_list = $translated_products_list;
         
            $this->update($department,$bc_id,$product_list,$request->language);
           
            session()->flash('changed_products',['translated'=>$changed_products]);
            session()->flash('changed_department',StrReplace::currency_underscore($department) );
            session()->flash('changed_language',$selected_lang['long'] );
            
            $this->company->update('product_lists');
            
            return redirect('/product_list');

        }
        
       
    }
    
    private function update($department,$bc_id,$product_list,$language)
    {
        
     
        /*NEW  LANGUAGE AND NEW LANGUAGE IS PREFERRED , => DOING  TRANSLATIONS OF OLD PRODUCT LISTS*/
        if(session()->has('old_product_list_needed'))
        {
            session()->pull('old_product_list_needed');
            $translator_language = session()->get('translator_language')['short'];
//            $preferred_changed = session()->pull('preferred_changed');
//
//            $all_product_lists =  DB::table('product_lists')
//                ->where('buyer_company_id',$bc_id)
//                ->where('department',$department)
//                ->pluck('product_list','language')
//                ->toArray() ;
            
            session()->put('new_preferred_product_list',$product_list);
          
            ProductList::change_preferred_language($translator_language,$language,$department);
           
            $this->company->update('product_lists');
          
        }
        
        else{
          
            /*BUYER UPDATING PRODUCT LIST IN PREFERRED LANGUAGE*/
            if($this->is_multi($product_list))
            {
                foreach($product_list as $language  =>  $list)
                {
                    DB::table('product_lists')
                        ->updateOrInsert(
                            ['language'=> $language,'department' => $department, 'buyer_id' => $this->role->get_owner_id(), 'buyer_company_id' => $bc_id],
                            ['product_list' =>  json_encode($list),'updated_at'=>date('Y-m-d H:i:s'),'created_at'=>date('Y-m-d H:i:s')]);
                }
              
            }
            else
            {
                /*BUYER UPDATING TRANSLATION PRODUCT LIST*/
                DB::table('product_lists')
                    ->updateOrInsert(
                        ['language'=> $language,'department' => $department, 'buyer_id' => $this->role->get_owner_id(), 'buyer_company_id' => $bc_id],
                        ['product_list' =>  json_encode($product_list),'updated_at'=>date('Y-m-d H:i:s'),'created_at'=>date('Y-m-d H:i:s')]);
            }
            
        }
        
        $this->company->update('product_lists');
        
        $details=[
            'n_link'                 =>   '/delivery_locations',
            'action'                 =>  'product_list_updated',
            'buyer_company_id'       =>  $bc_id,
            'language'               =>  $language,
            'department'             =>  $department,
            'subject'            =>  __('buyer_company updated product list in department.',
                [
                    'buyer_company'    =>  $this->companies()[$bc_id]->buyer_company_name,
                    'language'         =>  $language,
                    'department'       =>  $department
                ]),
        ];
        
        /////    PUSHER UPDATE SELLER'S BUYERS PRODUCT LISTS
        SellerNotificationEvent::dispatch($details);
    }
    private function check_for_changes($old_product_list,$new_products)
    {
        
        if(isset($old_product_list[session()->get('company_preferred_lan')]))
        {
            $changed_products   =   [];
            $old_product_pref_lang  =   $old_product_list[session()->get('company_preferred_lan')];
    
            for($key=0; $key< sizeof($new_products); $key++)
            {
                if(isset($old_product_pref_lang[$key]))
                {
                    if($old_product_pref_lang[$key] != $new_products[$key])
                    {
                        if($new_products[$key] == '')
                        {
                            $changed_products['deleted'][$old_product_pref_lang[$key]] = 1;
                            $changed_product_keys[$old_product_pref_lang[$key]]  =   $key;
                        }
                        else
                        {
                            $changed_products['edited'][$old_product_pref_lang[$key]] = $new_products[$key];
                            $changed_product_keys[$old_product_pref_lang[$key]]  =   $key;
                        }
                    }
                }
                else{
                    //// ADDITION OF NEW PRODUCTS
                    $changed_products['added'][$new_products[$key]] = $new_products[$key];
                }
        
            }
          
            foreach($old_product_list as $language   =>  $product_list)
            {
                foreach($changed_products as $change => $products)
                {
                    foreach($products as $old_product => $new_product)
                    {
                        //// PREFERRED LANGUAGE PRODUCT LIST
                        if($language == session()->get('company_preferred_lan'))
                        {
                            //// PRODUCT WAS DELETED  IN PREFERRED LANGUAGE PRODUCT LIST
                            /// SO WE WILL UNSET IT
                            if(isset($changed_products['deleted'][$old_product]) && !isset($changed_products['added'][$old_product]))
                            {
                                unset($old_product_list[$language][ $changed_product_keys[$old_product]] );
                            }
                            elseif(isset($changed_products['edited'][$old_product]))
                            {
                                $old_product_list[$language][ $changed_product_keys[$old_product]]   =    $changed_products['edited'][$old_product];
                               
                            }
                            elseif(isset($changed_products['added'][$old_product]) && !isset($changed_products['deleted'][$old_product]))
                            {
                                array_push($old_product_list[$language], $new_product);
                            }
    
                          
                        }
                        
                        //// TRANSLATIONS OF PRODUCT LIST
                        else
                        {
                           
                            //// IF PRODUCT WAS NOT DELETED  FROM PREFERRED LANGUAGE PRODUCT LIST, ONLY EDITED
                            if(isset($changed_products['edited'][$old_product]))
                            {
    
                                $old_product_list[$language][$new_product] = '';
                                //// UNSET OLD NAMES IN TRANSLATIONS  IF DELETED
                                unset($old_product_list[$language][$old_product]);
                            }
                            elseif(isset($changed_products['deleted'][$old_product]) && !isset($changed_products['added'][$old_product]))
                            {
                                //// UNSET OLD NAMES IN TRANSLATIONS  IF DELETED
                                unset($old_product_list[$language][$old_product]);
                            }
                            elseif(isset($changed_products['added'][$old_product]) && !isset($changed_products['deleted'][$old_product]))
                            {
                                ///// AND ADD NEW NAMES WITHOUT TRANSLATION IF ADDED
                                $old_product_list[$language][$new_product] = '';
                            }
                           
                        }
                    }
            
                }
        
            }
          // $old_product_list = array_values($old_product_list[session()->get('company_preferred_lan')]);
         
            return ['product_list'  => $old_product_list, 'changed_products'=>$changed_products];
        }
       /*BUYER IS CHANGING PREFERRED LANGUAGE AND HAD ONLY 1 LANGUAGE PRODUCT LIST*/
        elseif(isset($new_products))
        {
            session()->put('old_product_list_needed',session()->pull('old_product_list'));
            
            $product_list[session()->get('company_preferred_lan')] = array_filter($new_products,'strlen');
            
            return  ['product_list'  => $product_list, 'changed_products'=>[]];
        }
        
      
        
    }
    private function product_list()
    {
        
        
        return isset(   $this->companies()
            [session()->get('company_id')]
                ->product_lists) ?
            $this->companies()
            [session()->get('company_id')]
                ->product_lists
            : [];
    }
    private function old_product_list($department)
    {
        $product_list = [];
        $prod_list =  isset($this->product_list()[$department]) ? $this->product_list()[$department] : null;
        if($prod_list == null) return null;
        
        foreach($prod_list as $language => $list)
        {
            $product_list[$language]    =   json_decode( $list,true);
        }
        
      
       return   $product_list;
        
    }
    private function is_multi($array) {
        return (count($array) != count($array, 1));
    }
   
    

   
   
}
