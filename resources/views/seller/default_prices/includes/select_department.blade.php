{{ Form::select('department',$departments, isset($department) ? str_replace('_',' ',$department) : null,
									  ['id' => 'seller_price_lists_departments',
									  'data-seller_company_id'=>$seller_company_id,
									  'data-wrong'           =>       __('Something went wrong.'),
									  'data-later'          =>       ('Please try again later.') ,
									  'class' => 'form-control form-control-sm extended_price_list',
									  'placeholder'=>__('Please select department'),
									  
									  'required' => 'required']) }}
