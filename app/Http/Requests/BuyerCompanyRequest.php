<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class BuyerCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize( )
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
            
            'buyer_owner_name' => 'required|min:3',
           /* 'buyer_owner_email' => 'required|email|unique:buyer_companies,buyer_owner_email,'.\Auth::guard('buyer')->user()->id.',buyer_id',*/
            'buyer_owner_email' => 'required|email',
            'buyer_owner_phone_number' => 'required|min:3',
        
            'buyer_name' => 'required|min:3',
            'buyer_email' => 'required|email',
            'buyer_phone_number' => 'required|min:3',
        
            'buyer_accountant_name' => 'required|min:3',
            'buyer_accountant_email' => 'required|email',
            'buyer_accountant_phone_number' => 'required|min:3',
        
            'buyer_company_name' => 'required|min:3',
            'address' => 'required',
            'VAT_number' => 'required',
        
            'country' => 'required',
    
            'currencies' => 'required',
            'preferred_currency' => 'required',
            'languages' => 'required',
            'preferred_language' => 'required',
            
        ];
    }
}
