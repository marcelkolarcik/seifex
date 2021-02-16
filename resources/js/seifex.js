$.ajaxSetup({
    headers: {
        'X-CSRF-Token': $('input[name="_token"]').val()
    }
});



$(document).on("click", "#printer", function (e) {

    window.print();
});
////// BUYER / SELLER  SAME AS OWNER DETAILS ON CREATE COMPANY FORM
$(document).on("click", ".same_as_owner", function (e) {
    e.preventDefault();
   //swal.fire($(this).attr('id'));
    var type    =   $(this).attr('id');
    var owner  =   type.split("_")[0];

    var owner_name = $('#'+owner+'_owner_name').val();
    var owner_phone = $('#'+owner+'_owner_phone_number').val();
    var owner_email = $('#'+owner+'_owner_email').val();

    $('#'+type+'_name').val(owner_name);
    $('#'+type+'_phone_number').val(owner_phone);
    $('#'+type+'_email').val(owner_email);

  //  swal.fire(owner_name);

});

////  BUYER / SELLER  ACTIVATE / DEACTIVATE BUYER / SELLER
$(document).on("click", ".toggle_seller", function (e) {
    e.preventDefault();
    var url = $(this).attr('url');
    var department = $(this).attr('department');
    var seller_company_id = $(this).attr('seller_company_id');
    var buyer_company_id = $(this).attr('buyer_company_id');
    var title = $(this).attr('title');
    var text  = $(this).attr('text');
    var wrong = $(this).attr('wrong');
    var later  = $(this).attr('later');

    swal.fire({

        title: title,
        text: text,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'limegreen',
        cancelButtonColor: 'red',
        confirmButtonText:
            '<i class="fas fa-check"></i>',
        confirmButtonAriaLabel: 'Confirm.',
        cancelButtonText:
            '<i class="fas fa-times"></i>',
        cancelButtonAriaLabel: 'Cancel',
    }).then(function (result) {
        if (result.value) {

            $.ajax({
                type: "post",
                url: '/' + 'toggle_buyer_seller' + '/',
                data: { department: department, seller_company_id: seller_company_id, buyer_company_id:buyer_company_id ,url:url},

                success: function success(msg) {

                    if(msg['status']  ===  'updated')
                    {
                        swal.fire({
                            position: 'top-end',
                            type: 'success',
                            text: msg['text'],
                            showConfirmButton: false,
                            timer: 1500
                        });
                         location.reload();
                    }
                    else if(msg['status']  ===  'no price list')
                    {
                        swal.fire({
                            position: 'top-end',
                            type: 'warning',
                            text: msg['text'],
                            showConfirmButton: true,
                            timer: 3500
                        });
                    }

                    else if(msg['status']  ===  'error')
                    {
                        swal.fire({
                            position: 'top-end',
                            type: 'error',
                            title:  msg['title'],
                            text: msg['text'],
                            showConfirmButton: false,
                            timer: 2500

                        });
                    }

                },
                error: function error() {
                    swal.fire({
                        title: wrong,
                        text: later,
                        type: "error",
                        showConfirmButton: true,
                        // timer: 2500

                    });
                }
            });
        }
    });
});

////  BUYER / SELLER  REQUEST / SEND PRODUCT LIST
$(document).on("click", "#product_list_request", function (e) {


    var department = $(this).data('department');

    var seller_company_id = $(this).data('seller_company_id');
    var seller_email = $(this).data('seller_email');
    var seller_company_name = $(this).data('seller_company_name');

    var buyer_company_id = $(this).data('buyer_company_id');
    var buyer_email = $(this).data('buyer_email');
    var buyer_company_name = $(this).data('buyer_company_name');

    var country = $(this).data('country');
    var county = $(this).data('county');
    var county_l4 = $(this).data('county_l4');

    var delivery_location_id = $(this).data('delivery_location_id');

    var title = $(this).attr('title');
    var wrong = $(this).data('wrong');
    var later  = $(this).data('later');

    swal.fire({

        title: title,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'limegreen',
        cancelButtonColor: 'red',
        confirmButtonText:
            '<i class="fas fa-check"></i>',
        confirmButtonAriaLabel: 'Confirm.',
        cancelButtonText:
            '<i class="fas fa-times"></i>',
        cancelButtonAriaLabel: 'Cancel',
    }).then(function (result) {
        if (result.value) {

            $.ajax({
                type: "post",
                url: '/product_list_request',
                data: {
                    department: department,

                    seller_company_id: seller_company_id,
                    seller_email:seller_email,
                    seller_company_name:seller_company_name,

                    buyer_company_id:buyer_company_id,
                    buyer_email:buyer_email,
                    buyer_company_name:buyer_company_name,

                    country:country,
                    county:county,
                    county_l4:county_l4,

                    delivery_location_id:delivery_location_id },

                success: function success(msg) {
                    if(msg  ===  'updated')
                    {
                        swal.fire({
                            position: 'top-end',
                            type: 'success',
                            text: msg['text'],
                            showConfirmButton: false,
                            timer: 1500
                        });
                        location.reload();
                    }


                },
                error: function error() {
                    swal.fire({
                        title: wrong,
                        text: later,
                        type: "error",
                        showConfirmButton: false,
                        timer: 2500

                    });
                }
            });
        }
    });
});

////  BUYER / SELLER  ACTIVATE / DEACTIVATE STAFF DUTIES
$(document).on("click", ".duty", function (e) {

    var lead        =   $(this).data('lead');
    var duty_for    =   $(this).data('duty_for');
    var box_disabled    =   $(this).data('box_disabled');

    if(lead ===  1)
    {
        if( $(this). prop("checked") === false)
        {
            $('.'+duty_for).prop('checked', false).removeClass('text-primary').addClass('text-black-50');
            $('.'+duty_for+'-sub')/*.attr("disabled",true)*/.data("box_disabled",'yes');
            // $('.'+duty_for+'-sub').date("box_disabled",'no');

        }
        if( $(this). prop("checked") === true)
        {
            $('.'+duty_for+'-sub')/*.attr("disabled",false)*/.data("box_disabled",'no');
            $('.'+duty_for+'-lead-desc_icon').addClass('d-none');

        }
    }
    else if(lead === 0 &&  $('.'+duty_for+'-lead').prop('checked') === false) /* check if lead check box is checked if it is not then =>*/
    {

        $(this).prop('checked', false);
        $('.'+duty_for+'-lead-desc').removeClass('text-black-50').addClass('text-primary').fadeIn(150).removeClass('text-black-50').fadeOut(150).addClass('text-primary').fadeIn(150);
        $('.'+duty_for+'-lead-desc_icon').removeClass('d-none').fadeIn(150).fadeOut(150).fadeIn(150);
    }

});







