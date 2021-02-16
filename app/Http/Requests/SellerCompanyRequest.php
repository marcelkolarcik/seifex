<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SellerCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
   
    public function rules()
    {
    
      
        
        return [
            'seller_owner_name' => 'required|min:3',
            'seller_owner_email' => 'required|email|unique:seller_companies,seller_owner_email,'.\Auth::guard('seller')->user()->id.',seller_id',
            'seller_owner_phone_number' => 'required|min:3',
        
            'seller_name' => 'required|min:3',
            'seller_email' => 'required|email',
            'seller_phone_number' => 'required|min:3',
        
            'seller_accountant_name' => 'required|min:3',
            'seller_accountant_email' => 'required|email',
            'seller_accountant_phone_number' => 'required|min:3',
    
            'seller_delivery_name' => 'required|min:3',
            'seller_delivery_email' => 'required|email',
            'seller_delivery_phone_number' => 'required|min:3',
        
            'seller_company_name' => 'required|min:3',
            'address' => 'required',
            'VAT_number' => 'required',
        
            'country' => 'required',
        
            'last_order_at' => 'required',
            'payment_method' => 'required',
            'delivery_days'=>'required',
    
            'currencies' => 'required',
            'preferred_currency' => 'required',
            'languages' => 'required',
            'preferred_language' => 'required',
    
        ];
    }
}
