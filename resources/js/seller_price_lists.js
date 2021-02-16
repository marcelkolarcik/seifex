///// SELLER IS ADDING NEW PRODUCT TO DPL
var new_product = 0;
$(document).on("click", ".add_new_product_btn", function (e) {

    e.preventDefault();
    var optional               = $(this).attr('optional');
    var required               = $(this).attr('req');

    var old_products = parseInt($(this).attr('num_of_products'), 10);
    new_product++;
    var index = old_products + new_product;
    if(old_products > 1)
    {
        var old_index = index - 1;
    }
    else
    {
        old_index   =   index;
    }



    $('#default_seller_prices').find('tbody:last').append('<tr id="' + old_index + 'tr">'
        + '<td  >' + '<input    name = ' + old_index + '|product_name type="text" class="form-control form-control-sm bg-warning "   placeholder="'+ required +'" required ="'+ required +'" >' + '</td  >'
        + '<td  >' + '<input    name = ' + old_index + '|product_code type="text" class="form-control form-control-sm  "   placeholder="'+ optional +'"  >' + '</td  >'
        + '<td  >' + '<input name = ' + old_index + '|price_per_kg type="text" class="form-control form-control-sm bg-warning"  placeholder="'+ required +'" required ="required" >' + '</td>'
        + '<td >' + '<input name = ' + old_index + '|stock_level type="text" class="form-control form-control-sm bg-warning" placeholder="'+ required +'" required ="required" >' + '</td>'
        + '<td >' + '<input name = ' + old_index + '|low_stock type="text" class="form-control form-control-sm bg-warning " placeholder="'+ required +'" required ="required"  >' + '</td>'
        + '<td >' + '<input name = ' + old_index + '|extra_stock type="text" class="form-control form-control-sm  text-light" placeholder="'+ optional +'"  >' + '</td>'
        + '<td >' + '<input name = ' + old_index + '|type_brand type="text" class="form-control form-control-sm " placeholder="'+ optional +'" >' + '</td>'
        + '<td >' + '<input name = ' + old_index + '|box_size type="text" class="form-control form-control-sm" placeholder="'+ optional +'"  >' + '</td>'
        + '<td >' + '<input name = ' + old_index + '|box_price type="text" class="form-control form-control-sm" placeholder="'+ optional +'"  >' + '</td>'
        + '<td >' + '<input name = ' + old_index + '|additional_info type="text" class="form-control form-control-sm" placeholder="'+ optional +'"  >' + '</td>'
        + '<td >' + '<input name = ' + old_index + '|unset type="text" class="form-control form-control-sm" readonly="readonly"  >' + '</td>'
        + '<td >' + '<input name = ' + old_index + '|old_hash_name type="text" class="d-none" readonly="readonly"  >'+ '</td>'
        + '</tr>'
       );

});

/////// SELLER DELETING PRODUCT FROM DEFAULT PRICE LIST
$(document).on("click", ".delete_product", function (e) {
    /*$(".delete_btn").click(function (e) {*/
    e.preventDefault();
    var product_name = $(this).attr('name');
    var department = $(this).attr('department');
    var seller_company_id = $(this).attr('seller_company_id');
    var delete_string     = $(this).attr('delete_string');
    var wrong               = $(this).attr('wrong');
    var later               = $(this).attr('later');

    swal.fire({
        title: delete_string+' '+ product_name.split("+")[0] + ' ?',
        /* text: "You won't be able to revert this!",*/
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
                url: '/delete_product/',
                data: { product_name: product_name, department: department, seller_company_id:seller_company_id },

                success: function success(msg) {
                    if(msg['status']    ===     'no product')
                    {
                        swal.fire({
                            position: 'top-end',
                            type: 'error',
                            title: msg['title'],
                            text: msg['text'],
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(location.reload(),2000);
                    }
                    else
                    {
                        swal.fire({
                            position: 'top-end',
                            type: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                       setTimeout(location.reload(),2000);
                    }
                    // location.reload();
                },
                error: function()
                {
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

///// SELLER IS DELETING DEFAULT PRICE LIST
$(document).on("click", "#delete_price_list", function (e) {

    e.preventDefault();
    var department = $(this).attr('department');
    var seller_id = $(this).attr('seller_id');
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
                url: '/delete_department/seller/',
                data: { department: department, seller_id: seller_id, seller_company_id:seller_company_id },

                success: function success(msg) {
                    if(msg['status']    === 'deleted')
                    {
                        swal.fire({
                            position: 'top-end',
                            type: 'success',
                            text: msg['text'],
                            showConfirmButton: false,
                            timer: 1500
                        });
                        $('#product_forms').hide();
                        setTimeout(location.reload(),2000);
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

// ///////// SELLER DEFAULT PRICE LISTS
// $(document).on("change", "#seller_price_lists_departments", function (e) {
//     var seller_company_id = $(this).data('seller_company_id');
//     var wrong = $(this).data('wrong');
//     var later = $(this).data('later');
//     var department  =   $(this).val();
//
//     $.ajax({
//         type: "post",
//         url: '/prices',
//         data: { department: department, seller_company_id:seller_company_id },
//
//         success: function success(msg) {
//
//             $('#container').html('').html(msg);
//
//         },
//         error: function error() {
//             swal.fire({
//                 title: wrong,
//                 text: later,
//                 type: "error",
//                 showConfirmButton: false,
//                 timer: 1500
//
//             });
//         }
//     });
// });
// /////// SELLER DEFAULT PRICE LIST CURRENCY
// $(document).on("change", "#currency", function (e) {
//     var seller_company_id = $(this).data('seller_company_id');
//     var currency = $(this).val();
//     var department = $(this).data('department');
//
//     $.ajax({
//         type: "post",
//         url: '/prices',
//         data: { department: department, seller_company_id:seller_company_id ,currency:currency},
//
//         success: function success(msg) {
//
//             $('#container').html('').html(msg);
//
//         },
//         error: function error() {
//             swal.fire({
//                 title: wrong,
//                 text: later,
//                 type: "error",
//                 showConfirmButton: false,
//                 timer: 1500
//
//             });
//         }
//     });
// });
/*EXTENDED PRICE LIST CHANGE OF DEPATMENT, CURRENCY, LANGUAGE*/
$(document).on("change", ".extended_price_list", function (e){
    e.preventDefault();
    var selected_department = $('#seller_price_lists_departments').find("option:selected").val();
    var currency = $('#currency').find("option:selected").val();
    var language = $('#language').find("option:selected").val();


    $.ajax({
        type: "post",
        url: '/prices',
        data: { department: selected_department, currency:currency ,language:language},

        success: function success(msg) {

            $('#container').html('').html(msg);

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

});

//// SELLER -> APPLY PRICE FROM MULTI SELECT
$(document).on("change", ".multi", function (e) {
    e.preventDefault();
    var product_name = $(this).attr('id');

    var selected_product = $('#' + product_name).find("option:selected").text();
    var selected_value = $('#' + product_name).find("option:selected").val();

    var products_added = [];
    if (selected_value===0) {
        swal.fire('Please select one of the options');
    } else {
        $("#" + product_name).remove();
        var product_data = selected_value.split("|");

        $('#multi_seller_prices').append(

            '<tr id="' + selected_value + '">'

            + '<td >' + '<input name = products['+product_data[1]+'][product_name]' + ' type="text" class="form-control form-control-sm  " value = "' + product_data[1].replace(/_/g, ' ') + ' " placeholder="required" required ="required" readonly="readonly" >' + '</td  >'
            + '<td >' + '<input name = products['+product_data[1]+'][product_code]' + ' type="text" class="form-control form-control-sm  " value = "' + product_data[2] + '" placeholder="optional"  >' + '</td>'
            + '<td >' + '<input name = products['+product_data[1]+'][price_per_kg]'+ ' type="text" class="form-control form-control-sm  bg-warning " value = "' + product_data[3] + '" placeholder="required" required ="required" >' + '</td>'
            + '<td >' + '<input name = products['+product_data[1]+'][type_brand]' + ' type="text" class="form-control  form-control-sm " placeholder="optional" value = "' + product_data[4] + '" >' + '</td>'
            + '<td >' + '<input name = products['+product_data[1]+'][box_size]' + ' type="text" class="form-control  form-control-sm " placeholder="optional" value = "' + product_data[5] + '" >' + '</td>'
            + '<td >' + '<input name = products['+product_data[1]+'][box_price]' + ' type="text" class="form-control  form-control-sm " placeholder="optional" value = "' + product_data[6] + '" >' + '</td>'
            + '<td >' + '<input name = products['+product_data[1]+'][additional_info]' + ' type="text" class="form-control  form-control-sm " placeholder="optional" value = "' + product_data[7] + '" >' + '</td>'
            + '<td >' + '<input name = products['+product_data[1]+'][unset]' + ' type="text" class="form-control  form-control-sm " readonly="readonly"  value = "' + product_data[8] + '">' + '</td>'
            + '<td >'+'<input name = products['+product_data[1]+'][old_hash_name]' + ' type="text" class=" d-none" value = "' + product_data[0] + '" readonly="readonly"  >'+ '</td>'
             + '</tr>'
        );
    }
});

$(document).on("click", "#converter", function (e) {
    var seller_company_id = $(this).data('seller_company_id');
    var department = $(this).data('department');
    var currency = $(this).data('currency');
    var preferred_currency  =   $(this).data('preferred_currency');
    var rate  =   $('#rate').val();

    $.ajax({
        type: "post",
        url: '/prices',
        data: { department: department, seller_company_id:seller_company_id ,currency:currency,preferred_currency:preferred_currency,rate:rate},

        success: function success(msg) {
            if (typeof msg['error'] !== 'undefined')

            {
                swal.fire({
                    text: msg['error'],
                    type: "error",
                    showConfirmButton: true,

                });
               /* window.location.href = "/prices";*/
            }
            else
            {
                $('#container').html('').html(msg);
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
});
