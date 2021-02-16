///// SELLER CHANGING PAYMENT METHOD FOR BUYER
$(document).on("click", "#payment_frequency", function (e) {
    e.preventDefault();

    var buyer_company_id    = $(this).attr('buyer_company_id');
    var seller_company_id   = $(this).attr('seller_company_id');
    var payment_frequency   = $("input[name='payment_frequency']:checked").val();
    var department          = $(this).attr('department');
    var title               = $(this).attr('title');
    var text                = $(this).attr('text');
    var wrong               = $(this).attr('wrong');
    var later               = $(this).attr('later');

    swal.fire({

        title: title,
        text: text,
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
                url: '/payment_frequency',
                data: { buyer_company_id: buyer_company_id, seller_company_id: seller_company_id,payment_frequency:payment_frequency ,department:department},

                success: function success(msg) {
                    if(msg['status'] === 'updated')
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
                    else if(msg['status'] === 'not_updated')
                    {
                        swal.fire({
                            position: 'top-end',
                            type: 'error',
                            text: msg['text'],
                            showConfirmButton: true,
                        });


                    }


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


