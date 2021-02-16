//// SELLER -> DELETE DELIVERY LOCATION ddl
$(document).on("click", ".ddl", function (e) {
    e.preventDefault();
    var delivery_location_id = $(this).attr('delivery_location_id');
    var department = $(this).attr('department');
    var text = $(this).attr('text');
    var title = $(this).attr('title');
    var wrong               = $(this).attr('wrong');
    var later               = $(this).attr('later');

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
                url: '../delete_delivery_location',
                data: { department: department, delivery_location_id: delivery_location_id },

                success: function success(msg) {
                    if(msg['status']    === 'deleted')
                    {
                        swal.fire({
                            position: 'top-end',
                            type: 'success',
                            text: msg['location']+' '+msg['text'],
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(location.reload(),2000);
                    }



                },
                error: function error(msg) {
                    swal.fire({
                        title: wrong,
                        text: later,
                        type: "error",
                        showConfirmButton: false,
                        timer: 1500
                    });

                }
            });
        }
    });
});

//// SELLER IS DELETING  DEFAULT DELIVERY DEPARTMENT ddd
$(document).on("click", ".ddd", function (e) {
    e.preventDefault();
    var department = $(this).attr('department');
    var seller_company_id = $(this).attr('seller_company_id');
    var title = $(this).attr('title');
    var text = $(this).attr('text');
    var wrong               = $(this).attr('wrong');
    var later               = $(this).attr('later');
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
                url: '../delete_delivery_department',
                data: { department: department, seller_company_id:seller_company_id },

                success: function success(msg) {
                    if(msg['status']    === 'deleted')
                        swal.fire({
                            position: 'top-end',
                            type: 'success',
                            text: msg['department']+' '+msg['text'],
                            showConfirmButton: false,
                            timer: 1500
                        });

                    setTimeout(location.reload(),2000);
                },
                error: function error() {
                    swal.fire({
                        title: wrong,
                        text: later,
                        type: "error",
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        }
    });
});

///////// SELLER IS UPDATING DELIVERY DAYS FOR LOCATION
$(document).on("click", ".update_location_delivery_days", function (e){
    e.preventDefault();

    var delivery_location_id    =       $(this).attr('id');
    var updated_delivery_days   =       [];
    var delivery_days           =       'delivery_days_'+delivery_location_id;
    var location_name           =       $(this).data('location_name');
    var department              =       $(this).data('department');
    var title_text              =       $(this).data('text');
    var wrong                   =       $(this).data('wrong');
    var later                   =       $(this).data('later');

    $.each($("input[name='"+delivery_days+"']:checked"), function(){
        updated_delivery_days.push($(this).val());
    });



    swal.fire({
        title: title_text,
        text: location_name,
        type: 'info',
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
                url: '/update_location_delivery_days/',
                data: { delivery_location_id: delivery_location_id,updated_delivery_days:updated_delivery_days,department:department },

                success: function success(msg) {

                    if(msg['status'] === 'updated')
                    {
                        swal.fire({
                            position: 'top-end',
                            type: 'success',
                            text:msg['text'],
                            showConfirmButton: false,
                            timer: 1500
                        })
                       setTimeout(location.reload(),2000);
                    }
                    else if(msg['status'] === 'error')
                    {
                        swal.fire({
                            position: 'top-end',
                            type: 'error',
                            title: msg['status'],
                            text:msg['text'],
                            showConfirmButton: true,
                            timer: 1500
                        });
                      setTimeout(location.reload(),2000);
                    }
                },
                error: function()
                {
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

///////// SELLER IS UPDATING DELIVERY DAYS FOR BUYER
$(document).on("click", ".update_buyer_delivery_days", function (e){
    e.preventDefault();

    var buyer_company_id    =    $(this).attr('buyer_company_id');
    var seller_company_id    =    $(this).attr('seller_company_id');
    var department    =    $(this).attr('department');
    var buyer_name   =    $(this).attr('buyer_name');
    var title   =    $(this).attr('title');
    var updated_delivery_days = [];
    var delivery_days   =   'delivery_days[]';
    var wrong = $(this).attr('wrong');
    var later  = $(this).attr('later');

    $.each($("input[name='"+delivery_days+"']:checked"), function(){
        updated_delivery_days.push($(this).val());
    });



    swal.fire({
        title: title,
        text: buyer_name,
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
                url: '/update_buyer_delivery_days/',
                data: { department: department,buyer_company_id: buyer_company_id,seller_company_id: seller_company_id,updated_delivery_days:updated_delivery_days },

                success: function success(msg) {
                    if(msg['status'] === 'updated')
                    {
                        swal.fire({
                            position: 'top-end',
                            type: 'success',
                            text:msg['text'],
                            showConfirmButton: false,
                            timer: 1500
                        })
                        setTimeout(location.reload(),2000);
                    }
                    else if(msg['status'] === 'not updated')
                    {
                        swal.fire({
                            position: 'top-end',
                            type: 'error',
                            text:msg['text'],
                            showConfirmButton: true,
                            timer: 1500
                        })
                        setTimeout(location.reload(),2000);
                    }
                },
                error:  function error() {
                    swal.fire({
                        title: wrong,
                        text: later,
                        type: "error",
                        showConfirmButton: false,
                        timer: 2500

                    })

                }
            });
        }
    });

});
