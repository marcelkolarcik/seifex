<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DutyRequest extends FormRequest
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
     * @return array'
     */
    public function rules()
    {
        return [
            'role'              =>      'required|min:3',
            'duty_name'         =>      'required|min:3',
            'duty_description'  =>      'required|min:3',
            'duty_for'          =>      'required|min:3',
            'lead_duty'         =>      'required|boolean',

        ];
    }
}
