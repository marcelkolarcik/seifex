////// BUYER IS DELETING HIS DEPARTMENT
$(document).on("click", "#delete_department", function (e) {

    e.preventDefault();

    var department = $('#buyer_product_list_departments').find("option:selected").text();

    var buyer_company_id = $(this).attr('buyer_company_id');
    var title = $(this).attr('title');
    var wrong = $(this).attr('wrong');
    var later  = $(this).attr('later');

    swal.fire({

        title: title+' '+department+' ?',
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
                url: '/product_list/delete',
                data: { department: department, buyer_company_id: buyer_company_id },

                success: function success(msg) {
                    if(msg['status'] === 'deleted' )
                        swal.fire({
                            position: 'top-end',
                            type: 'success',
                            title: msg['text'] ,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    $('#product_forms').hide();
                    location.reload();
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

///////// BUYER DEFAULT PRODUCT LISTS
$(document).on("change", ".product_list", function (e) {
    e.preventDefault();


    var selected_department = $('#buyer_product_list_departments').val();

    ////// BUYER HAS MORE THEN 1 LANGUAGE
    var selected_lang = $('#buyer_languages').find("option:selected").val();

    ////// BUYER HAS 1 LANGUAGE
    if(typeof selected_lang === "undefined")
    {
         selected_lang = $('#buyer_languages').val();
    }

    //// placeholder
    if (selected_department === '') {

        $('.check_div').hide();
        $('.product_div').hide();
        $('#product_table').hide();
    } else {
        $('.check_div').show();
        $('.product_div').show();

    }

    if(selected_lang !== "" && selected_department !== '')
    {

        $.ajax({
            type: "get",
            url: '',
            data: { selected_department: selected_department, selected_lang:selected_lang },
            success: function success(msg) {
                if(msg === 'preferred_empty')
                {
                    $('#delete_department').addClass('d-none');
                    $('#product_list').removeClass('d-none');
                    $('#product_list_current').removeClass('d-none').show().load('/product_list/show?dep='+selected_department.replace(' ','_').replace(' ','_')+'&language='+selected_lang);
                    $('.check_div').removeClass('d-none');
                }
                else if (msg === 'empty') {
                    $('.check_div').addClass('d-none');
                    $('#delete_department').addClass('d-none');
                    $('#product_list').addClass('d-none');
                    $('#product_list').text('');
                    $('#product_list_current').removeClass('d-none').show().load('/product_list/show?dep='+selected_department.replace(' ','_').replace(' ','_')+'&language='+selected_lang);

                }
                else
                {
                    $('#delete_department').removeClass('d-none');
                    if( msg ===   'preferred')
                    {
                        $('#product_list').removeClass('d-none');

                    }
                    else {
                        $('#product_list').addClass('d-none');

                    }
                    $('.check_div').removeClass('d-none');

                    $('#changed_products').hide();
                    $('#product_list_current').show().load('/product_list/show?dep='+selected_department.replace(' ','_').replace(' ','_')+'&language='+selected_lang);
                    $('#product_table').hide();
                }

            },
            error: function error() {
                $('#product_list').text('');
                $('#delete_department').addClass('d-none');
            }
        });
    }



});

///// show product moves ACTIVATED / DEACTIVATED
$(document).on("click",".moves", function (e){
    e.preventDefault();

    var div_id  =   $(this).attr('id');
    if($('.'+div_id).hasClass('d-none'))
    {
        $('.'+div_id).removeClass('d-none');
    }
    else

    {
        $('.'+div_id).addClass('d-none');
    }

})

///// BUYER -> ADD / REMOVE PRODUCT FROM PRICE LIST
$(document).on("click",".activate", function (e) {


    var product = $(this).attr('product');
    var seller_company_id = $(this).attr('seller_company_id');
    var buyer_company_id = $(this).attr('buyer_company_id');
    var department = $(this).attr('department');
    var action = $(this).attr('action');
    var title_text = $(this).attr('title_text');
    var text = $(this).attr('text');
    var wrong = $(this).attr('wrong');
    var later  = $(this).attr('later');

    swal.fire({

        title: title_text,
        text: text,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'limegreen',
        cancelButtonColor: 'red',
        confirmButtonText:
            '&#9989;',
        confirmButtonAriaLabel: 'Confirm.',
        cancelButtonText:
            '&#9940;',
        cancelButtonAriaLabel: 'Cancel',
    }).then(function (result) {
        if (result.value) {

            $.ajax({
                type: "post",
                url: '/update_product',
                data: {product:product, department: department, seller_company_id: seller_company_id, buyer_company_id:buyer_company_id ,action:action},

                success: function success(msg) {
                    if(msg['status']  ===  'updated')
                    {
                        swal.fire({
                            position: 'top-end',
                            type: 'success',
                            text: msg['text'],
                            showConfirmButton: true,
                            timer: 1500,
                        });
                        setTimeout(location.reload(),3000);
                    }
                    else if(msg['status']  ===  'no product')
                    {
                        swal.fire({
                            position: 'top-end',
                            type: 'warning',
                            text: msg['text'],
                            showConfirmButton: true,
                            //timer: 1500
                        });
                    }
                  //  setTimeout(location.reload(),2000);

                },
                error: function error() {
                    swal.fire({
                        title: wrong,
                        text: later,
                        type: error,
                        showConfirmButton: false,
                      //  timer: 1500
                    });

                }
            });
        }
    });
});
