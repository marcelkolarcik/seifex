$(document).on("click","#order_placed",function (e){
    e.preventDefault();



    var department          =   $(this).attr('department');
    var buyer_company_id    =   $(this).attr('buyer_company_id');
    var order = $("#online_order").serialize();
    var order_withoutEmpties = order.replace(/[^&]+=\.?(?:&|$)/g, '');
    var no_product =   $(this).attr('no_product');
    var moment =   $(this).attr('moment');
    var ordering =   $(this).attr('ordering');
    var wrong   =   $(this).attr('wrong');
    var later   =   $(this).attr('later');


    //// NO PRODUCT SELECTED
    if (order_withoutEmpties.split("&")[1] === '') {
        swal.fire({
            text: no_product,
            type: "warning",
            button: "OK"
        });
    } else {
        swal.fire({
            title: moment,
            text: ordering,
            type: "info",
            button: false
        });
        $.ajax({
            type: "post",
            url: '/order_placed',
            data: { order: order_withoutEmpties , department:department , buyer_company_id:buyer_company_id},
            success: function success(msg) {

                if (msg === 'order empty') {
                    swal.fire({
                        text: no_product,
                        type: "warning",
                        button: "Ok"
                    });
                } else if (msg['status'] === 'order_placed') {

                    swal.fire({
                        type: "success",
                        html:
                             msg['text']  + '</br >' + msg['sellers'] + '</br >',
                        timer: 1500
                    });


                    if(msg['can_see_orders'])
                    {
                        window.location.replace("/orders/" + department+"/"+buyer_company_id);
                    }
                    else
                    {
                        location.reload();
                    }

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

$(document).on("click", ".order_dispatched", function (e) {

    e.preventDefault();

    var order_id = $(this).attr('id');
    var wrong   =   $(this).data('wrong');
    var later   =   $(this).data('later');
    var buyer_company_id   =   $(this).data('buyer_company_id');
    var buyer_company_name   =   $(this).data('buyer_company_name');
    var buyer_email   =   $(this).data('buyer_email');
    var seller_company_id   =   $(this).data('seller_company_id');
    var seller_company_name   =   $(this).data('seller_company_name');
    var seller_email   =   $(this).data('seller_email');
    var department   =   $(this).data('department');

    $.ajax({
        type: "post",
        url: '/order_dispatched',
        data: {
                order_id            :   order_id,
                buyer_company_id    :   buyer_company_id,
                buyer_company_name  :   buyer_company_name,
                buyer_email         :   buyer_email,
                seller_company_id   :   seller_company_id,
                seller_company_name :   seller_company_name,
                seller_email        :   seller_email,
                department          :   department

        },
        success: function success(msg) {

            if (msg['status'] === 'dispatched') {


                swal.fire({
                    title: msg['title'],
                    text: msg['text'],
                    type: "success",
                    button: "OK",
                    timer: 2500
                });
                setTimeout(function(){location.reload()}, 3000);
                $('#' + order_id).removeClass("btn-success").addClass('btn-danger').html('Change status to delivered');
                $('.delivered').removeClass('hidden').attr("delivered", "");
            } else {

                swal.fire({
                    title: msg['title_error'],
                    text: msg['text_error'],
                    type: "error",
                    button: "OK",
                    timer: 2500
                });
                /*location.reload();
                $('#' + order_id).removeClass("btn-danger").addClass('btn-success').html('Change status to dispatched');
                $('.delivered').addClass('hidden');*/
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
});

$(document).on("click", ".order_delivered", function (e) {

    e.preventDefault();

    var order_id = $(this).attr('id');
    var wrong   =   $(this).data('wrong');
    var later   =   $(this).data('later');
    var buyer_company_id   =   $(this).data('buyer_company_id');
    var buyer_company_name   =   $(this).data('buyer_company_name');
    var buyer_email   =   $(this).data('buyer_email');
    var seller_company_id   =   $(this).data('seller_company_id');
    var seller_company_name   =   $(this).data('seller_company_name');
    var seller_email   =   $(this).data('seller_email');
    var department   =   $(this).data('department');

    if ($(this).attr('prepped') === '') {
        var prepped = 1;
    } else {
        prepped = 0;
    }
    if ($(this).attr('delivered') === '') {
        var delivered = 1;
    } else {
        delivered = 0;
    }

    $.ajax({
        type: "post",
        url: '/order_delivered',
        data: {
            order_id            :   order_id,
            prepped             :   prepped,
            delivered           :   delivered ,
            buyer_company_id    :   buyer_company_id,
            buyer_company_name  :   buyer_company_name,
            buyer_email         :   buyer_email,
            seller_company_id   :   seller_company_id,
            seller_company_name :   seller_company_name,
            seller_email        :   seller_email,
            department          :   department},
        success: function success(msg) {
            /// close progress bar

            if (msg['status'] === 'delivered') {
                $(this).attr("delivered", "");

                swal.fire({
                    title: msg['title'],
                    text: msg['text'],
                    type: "success",
                    button: "OK",
                    timer: 2500
                });
               location.reload();
                $('.delivered').removeClass("btn-success").addClass('btn-danger').html('Delivered').attr('disabled', 'disabled');
                $('.prepped').addClass('hidden');
            } else {
                $(this).removeAttr("delivered");

                swal.fire({
                    title: msg['title_error'],
                    text: msg['text_error'],
                    type: "error",
                    button: "OK",
                    timer: 2500
                });
                location.reload();
                $('.delivered').removeClass("btn-danger").addClass('btn-success').html('Change status to delivered');
                $('.prepped').removeClass('hidden');
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
});

$(document).on("click", ".order_delivery_confirmed", function (e) {
    e.preventDefault();

    var order_id = $(this).attr('id');
    var wrong   =   $(this).attr('wrong');
    var later   =   $(this).attr('later');
    var comment = $('#order_comment').val();
    var buyer_company_id   =   $(this).data('buyer_company_id');
    var buyer_company_name   =   $(this).data('buyer_company_name');
    var buyer_email   =   $(this).data('buyer_email');
    var seller_company_id   =   $(this).data('seller_company_id');
    var seller_company_name   =   $(this).data('seller_company_name');
    var seller_email   =   $(this).data('seller_email');
    var department   =   $(this).data('department');
    $.ajax({
        type: "post",
        url: '/order_delivery_confirmed',
        data: {
            comment             :   comment,
            order_id            :   order_id,
            buyer_company_id    :   buyer_company_id,
            buyer_company_name  :   buyer_company_name,
            buyer_email         :   buyer_email,
            seller_company_id   :   seller_company_id,
            seller_company_name :   seller_company_name,
            seller_email        :   seller_email,
            department          :   department},
        success: function success(msg) {

            if (msg['status'] === 'buyer_confirmed_delivery') {

                swal.fire({
                    title: msg['title'],
                    text: msg['text'],
                    type: "success",
                    button: "OK",
                    timer: 2500
                });
                window.location.replace("/orders" );
            }
            else{
                swal.fire({
                    title: msg['title_error'],
                    text: msg['text_error'],
                    type: "error",
                    button: "OK",
                    timer: 2500
                });
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
    /**/

});

$(document).on("click", ".display_order", function (e) {
    e.preventDefault();

    var order_id = $(this).attr('id');
    var order = '';
    var when = $(this).attr('when');
    var seller = $(this).attr('seller');
    // alert(when);
    $.ajax({
        type: "post",
        url: '../display_order',
        data: { order_id: order_id },
        dataType: 'json',
        success: function success(msg) {
            var json = $.parseJSON(msg);
            $(json).each(function (i, val) {
                $.each(val, function (k, v) {
                    order += k + " x " + v + "\n";
                    console.log(k + " : " + v);
                });
            });

            if (msg === 'order empty') {
                swal.fire({
                    title: "What are you doing?",
                    text: "There is no such an order !",
                    icon: "error",
                    button: "I'm sorry Boss!!!"
                });
            } else {
                /*$('#Form_'+selectedSeller.replace(" ","_")).delay(500).fadeOut(500,0);*/
                swal.fire({
                    title: "Ordering " + " from " + seller + " " + when,
                    text: order,
                    /*  icon: "info",*/
                    button: "Perfect"
                });
            }
        },
        error: function error(msg) {
            swal.fire({
                title: " Something went wrong",
                text: "Can't display order at the moment",
                icon: "error",
                button: "Ooops !!!"

            });
        }
    });
});

$(document).on("click",".department", function (e) {
    e.preventDefault();

    var department                  = $(this).data('department');
    var buyer_company_id            = $(this).data('buyer_company_id');
    $('.department').removeClass('bg-light_green');
    $(this).addClass('bg-light_green');
    ///setters
    $('.delivery_date').data('department',department).data('buyer_company_id',buyer_company_id); //setter

});
var count = 0; /* for shopping list*/
$(document).on("click",".delivery_date", function (e){
   e.preventDefault();

   var day_num              =   $(this).data('day_num');
   var buyer_company_id     =   $(this).data('buyer_company_id');
   var department           =   $(this).data('department');
   var en_timestamp         =   $(this).data('delivery_date');
   var wrong                =   $(this).data('wrong');
   var later                =   $(this).data('later');

    count = 0;
    $("#orders_counter").html(count);

    $(this).addClass('bg-light_green');


   if(department === '' ) {


       swal.fire({
           title: 'Please select department first !',
           type: "warning",
           button: "OK",

       });

   }
   else
   {
       $(this).addClass('btn-success');
       $.ajax({
           type: "post",
           url: '/ordering/sellers',
           data: { day_num: day_num,buyer_company_id:buyer_company_id,department:department ,en_timestamp:en_timestamp},
           success: function success(msg) {

               if (msg['status'] === 'sellers') {

                   $('#form').load('/ordering/form');

               }
               else if(msg['status'] === 'no_sellers')
                   {
                   swal.fire({
                       title: msg['title'],
                       text: msg['text'],
                       type: "info",
                       button: "OK",
                       timer: 2500
                   });
                       setTimeout(function(){location.reload()}, 2500);
               }
               else if(msg['status'] === 'no_preferred_sellers')
               {

                   $('#alternative').load('/ordering/alternative');

                   // setTimeout(function(){location.reload()}, 2500);
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
       })
   }


});

$(document).on("click",".alternative", function (e){
    e.preventDefault();
    var currency    =   $(this).data('currency');
    var language    =   $(this).data('language');


    $('#form').load('/ordering/form?currency='+currency+'&language='+language+'');
});

$(document).on("click", "#orders_icon", function (e) {

    e.preventDefault();

    $("#orders_holder").toggleClass('display_none','display_notifications');

});

var total = 0;
$(document).on("change",".product", function (e){
    e.preventDefault();

    var name                 =   $(this).data('name');
    var hash_name            =   $(this).data('hash_name');

    var price_per_kg         =   $(this).data('price_per_kg');
    var box_size             =   $(this).data('box_size');
    var value                =   $(this).val();

    $('.'+hash_name).val(value);

    /* Create total variable and keep adding to it, create box size as default == 1, if ordering per kg */
    $(this).addClass('btn-success');

    $("#orders_icon").removeClass('invisible').addClass('visible');

    /// ORDERING PER KG
    if( typeof(box_size) === "undefined")  {
    $("#placed_orders").prepend(
        '<a class="list-group-item d-flex justify-content-between align-items-center" href="">'
        +name+' - '+value+' kg x '+price_per_kg +' '+value * price_per_kg
        + '</a>');


    }
    else{
        $("#placed_orders").prepend(
            '<a class="list-group-item d-flex justify-content-between align-items-center" href="">'
            +name+' - '+value+' kg x '+price_per_kg +' x '+box_size +' box size - '+ value * price_per_kg * box_size
            + '</a>');


    }
    if( typeof(box_size) === "undefined")  {
        box_size    =   1;
    }
    total   += value * price_per_kg * box_size;

    count++;
    $("#orders_counter").html(count);
    $("#total").html(total);




});

$(document).on("click", ".order_type", function (e) {
    $('.order_type').removeClass('btn-primary text-light').addClass('btn-outline-primary');
    $(this).removeClass('btn-outline-primary').addClass("btn-primary text-light");

});
