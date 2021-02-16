////    SEND INVOICE TO BUYER   ////
$(document).on("click", ".send_invoice", function (e) {
    e.preventDefault();

    var department          = $(this).attr('department');
    var buyer_company_id    = $(this).attr('buyer_company_id');
    var seller_company_id   = $(this).attr('seller_company_id');
    var period              = $(this).attr('period');
    var order_ids           = $(this).attr('order_ids');
    var invoice_freq        = $(this).attr('invoice_freq');
    var title               = $(this).attr('title');
    var later               =   $(this).attr('later');
    var wrong               =   $(this).attr('wrong');


    swal.fire({

        title: title,
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
                url: '/send_invoice',
                data: { department: department, buyer_company_id: buyer_company_id, seller_company_id: seller_company_id, period:period,order_ids:order_ids,invoice_freq:invoice_freq },

                success: function success(msg) {
                    if(msg['status'] === 'sent')
                    {
                        swal.fire({
                            position: 'top-end',
                            type: 'success',
                            text: msg['title'],
                            showConfirmButton: false,
                            timer: 1500
                        });

                        location.reload();
                    }
                    else if(msg['status'] === 'not sent'){
                        swal.fire({
                            position: 'top-end',
                            type: 'error',
                            title: msg['title'],
                            text: msg['text'],
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }

                },
                error: function error(msg) {
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

/////    MARK INVOICE AS PAID
$(document).on("click", ".mark_as_paid_invoice", function (e) {
    e.preventDefault();

    var buyer_company_id    = $(this).attr('buyer_company_id');
    var seller_company_id   = $(this).attr('seller_company_id');
    var period              = $(this).attr('period');
    var invoice_id          = $(this).attr('id');
    var title               = $(this).attr('title');
    var later               =   $(this).attr('later');
    var wrong               =   $(this).attr('wrong');

    swal.fire({

        title: title,
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
                url: '/mark_as_paid_invoice',
                data: { buyer_company_id: buyer_company_id, seller_company_id: seller_company_id, period:period,invoice_id:invoice_id },

                success: function success(msg) {
                    if(msg['status'] === 'marked_as_paid')
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
                    else if(msg['status'] === 'not_marked_as_paid')
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
                error: function error(msg) {
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

/////   CONFIRM INVOICE AS PAID
$(document).on("click", ".confirm_invoice", function (e) {
    e.preventDefault();

    var invoice_id          =   $(this).attr('id');
    var title               =   $(this).attr('title');
    var later               =   $(this).attr('later');
    var wrong               =   $(this).attr('wrong');

    swal.fire({

        title: title,
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
                url: '/confirm_invoice',
                data: { invoice_id: invoice_id },

                success: function success(msg) {
                    if(msg['status'] === 'confirmed_as_paid')
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
                    else if(msg['status'] === 'not_confirmed_as_paid')
                    {
                        swal.fire({
                            position: 'top-end',
                            type: 'error',
                            text: msg['text'],
                            showConfirmButton: false,
                            timer: 1500
                        });

                        location.reload();
                    }

                },
                error: function error(msg) {
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
