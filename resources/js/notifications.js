$(document).on("click", "#notification_icon", function (e) {

    e.preventDefault();

    $("#notifications_holder").toggleClass('display_none','display_notifications');

});

var count = 0;


// window.Echo.private('orderPlaced').listen('OrderPlacedEvent', e => {
//     $.ajax({
//         type: "post",
//         url: '/pusher_order_placed',
//         data: { order: e.order },
//
//         success: function success(msg) {
//             if(msg === 'ok')
//             {
//                 var link    =   '/order/'+e.order.id+'/'+e.order.seller_company_id;
//                 $("#notification_icon").removeClass('invisible').addClass('visible');
//                 $("#notifications").prepend(
//                     '<a class="list-group-item d-flex justify-content-between align-items-center" href="'+link+'">'
//                     + e.order.subject
//                     + '</a>');
//                 count++;
//                 $("#notification_counter").html(count);
//
//             }
//
//         },
//     });
//
// });
// window.Echo.private(`orderUpdates`).listen('OrderStatusUpdatedEvent', e => {
//     $.ajax({
//         type: "post",
//         url: '/pusher_order_dispatched',
//         data: { order: e.order },
//
//         success: function success(msg) {
//             if(msg === 'ok')
//             {
//                 var link    =   '/order/'+e.order.id+'/'+e.order.buyer_company_id;
//
//                 $("#notification_icon").removeClass('invisible').addClass('visible');
//
//                 if(e.order.action === 'order_dispatched')
//                 {
//                     $("#notifications").prepend(
//                         '<a class="list-group-item d-flex justify-content-between align-items-center" href="'+link+'">'
//                         + e.order.subject
//                         + '</a>');
//
//                 }
//                 else if(e.order.action ===  'order_delivered')
//                 {
//                     $("#notifications").prepend(
//                         '<a class="list-group-item d-flex justify-content-between align-items-center" href="'+link+'">'
//                         + '<span >'+e.order.subject+'<small class="text-danger">'+' '+ e.order.confirm +'</small>'+'</span>'
//                         + '</a>');
//                 }
//                 count++;
//                 $("#notification_counter").html(count);
//             }
//
//         },
//     });
// });
// window.Echo.private('orderConfirmed').listen('OrderDeliveryConfirmedEvent', e => {
//     $.ajax({
//         type: "post",
//         url: '/pusher_order_delivery_confirmed',
//         data: { order: e.order },
//
//         success: function success(msg) {
//             if(msg === 'ok')
//             {
//
//                 var link    =   '/order/'+e.order.id+'/'+e.order.seller_company_id;
//                 $("#notification_icon").removeClass('invisible').addClass('visible');
//                 $("#notifications").prepend(
//                     '<a class="list-group-item d-flex justify-content-between align-items-center" href="'+link+'">'
//                     + e.order.subject
//                     + '</a>');
//                 count++;
//                 $("#notification_counter").html(count);
//             }
//
//         },
//     });
//
// });

// window.Echo.private('invoiceSent').listen('InvoiceSentEvent', e => {
//     $.ajax({
//         type: "post",
//         url: '/pusher_invoice_sent',
//         data: { invoice: e.invoice },
//
//         success: function success(msg) {
//             if(msg === 'ok')
//             {
//                 var link    =   '/invoice/'+e.invoice.id+'/'+e.invoice.buyer_company_id;
//                 $("#notification_icon").removeClass('invisible').addClass('visible');
//                 $("#notifications").prepend(
//                     '<a class="list-group-item d-flex justify-content-between align-items-center" href="'+link+'">'
//                     + e.invoice.subject
//                     + '</a>');
//                 count++;
//                 $("#notification_counter").html(count);
//               /*  console.log('invoice to be paid');
//                 console.log(e);*/
//
//             }
//
//         },
//     });
//
// });
// window.Echo.private('invoicePaid').listen('InvoicePaidEvent', e => {
//     $.ajax({
//         type: "post",
//         url: '/pusher_invoice_paid',
//         data: { invoice: e.invoice },
//
//         success: function success(msg) {
//             if(msg === 'ok')
//             {
//                 var link    =   '/invoice/'+e.invoice.id+'/'+e.invoice.seller_company_id;
//                 $("#notification_icon").removeClass('invisible').addClass('visible');
//                 $("#notifications").prepend(
//                     '<a class="list-group-item d-flex justify-content-between align-items-center" href="'+link+'">'
//                     + e.invoice.subject
//                     + '</a>');
//                 count++;
//                 $("#notification_counter").html(count);
//             }
//
//         },
//     });
//
// });
// window.Echo.private('invoiceConfirmed').listen('InvoiceConfirmedEvent', e => {
//     $.ajax({
//         type: "post",
//         url: '/pusher_invoice_confirmed',
//         data: { invoice: e.invoice },
//
//         success: function success(msg) {
//             if(msg === 'ok')
//             {
//                 var link    =   '/invoice/'+e.invoice.id+'/'+e.invoice.buyer_company_id;
//                 $("#notification_icon").removeClass('invisible').addClass('visible');
//                 $("#notifications").prepend(
//                     '<a class="list-group-item d-flex justify-content-between align-items-center" href="'+link+'">'
//                     + e.invoice.subject
//                     + '</a>');
//                 count++;
//                 $("#notification_counter").html(count);
//             }
//
//         },
//     });
//
// });
// window.Echo.private('ProductList').listen('ProductListEvent', e => {
//     $.ajax({
//         type: "post",
//         url: '/pusher_product_list',
//         data: { details: e.details },
//
//         success: function success(msg) {
//
//             if(msg === 'ok')
//             {
//                 if(e.details.activator  === 'buyer')/*seller is listening*/
//                 {
//                     var link    =   '/pricing/'+e.details.buyer_company_id+'/'+e.details.department+'/'+e.details.seller_company_id;
//                 }
//                 if(e.details.activator  === 'seller')/*buyer is listening*/
//                 {
//                     var link    =   '/buyer';
//                 }
//
//                 $("#notification_icon").removeClass('invisible').addClass('visible');
//                 $("#notifications").prepend(
//                     '<a class="list-group-item d-flex justify-content-between align-items-center" href="'+link+'">'
//                     + e.details.subject
//                     + '</a>');
//                 count++;
//                 $("#notification_counter").html(count);
//             }
//
//         },
//     });});
// window.Echo.private('CompanyActivation').listen('NotificationEvent_', e => {
//     $.ajax({
//         type: "post",
//         url: '/pusher_company_activation',
//         data: { details: e.details },
//
//         success: function success(msg) {
//             if(msg === 'ok')
//             {
//                 if(e.details.activator  === 'buyer')/*seller is listening*/
//                 {
//                     var link    =   '/pricing/'+e.details.buyer_company_id+'/'+e.details.department+'/'+e.details.seller_company_id;
//                 }
//                 if(e.details.activator  === 'seller')/*buyer is listening*/
//                 {
//                     var link    =   '/department/'+e.details.department+'/'+e.details.buyer_company_id;
//                 }
//
//                 $("#notification_icon").removeClass('invisible').addClass('visible');
//                 $("#notifications").prepend(
//                     '<a class="list-group-item d-flex justify-content-between align-items-center" href="'+link+'">'
//                     +e.details.subject
//                     + '</a>');
//                 count++;
//                 $("#notification_counter").html(count);
//             }
//         },
//     });
// });


window.Echo.private('BuyerNotifications').listen('BuyerNotificationEvent', e => {
    $.ajax({
        type: "post",
        url: '/pusher_notification',
        data: { details: e.details },

        success: function success(msg) {
            if(msg === 'ok')
            {

                $("#notification_icon").removeClass('invisible').addClass('visible');
                $("#notifications").prepend(
                    '<a class="list-group-item d-flex justify-content-between align-items-center" href="'+e.details.n_link+'">'
                    +e.details.subject
                    + '</a>');
                count++;
                $("#notification_counter").html(count);
            }
        },
    });
});
window.Echo.private('SellerNotifications').listen('SellerNotificationEvent', e => {
    $.ajax({
        type: "post",
        url: '/pusher_notification',
        data: { details: e.details },

        success: function success(msg) {
            if(msg === 'ok')
            {
                $("#notification_icon").removeClass('invisible').addClass('visible');
                $("#notifications").prepend(
                    '<a class="list-group-item d-flex justify-content-between align-items-center" href="'+e.details.n_link+'">'
                    +e.details.subject
                    + '</a>');
                count++;
                $("#notification_counter").html(count);
            }
        },
    });
});
// window.Echo.private('ProductMoved').listen('ProductMovedEvent', e => {
//     $.ajax({
//         type: "post",
//         url: '/pusher_product_moved',
//         data: { details: e.details },
//
//         success: function success(msg) {
//             if(msg === 'ok')
//             {
//                 var link    =   '/pricing/'+e.details.buyer_company_id+'/'+e.details.department+'/'+e.details.seller_company_id;
//
//                 $("#notification_icon").removeClass('invisible').addClass('visible');
//                 $("#notifications").prepend(
//                     '<a class="list-group-item d-flex justify-content-between align-items-center" href="'+link+'">'
//                     + e.details.subject
//                     + '</a>');
//                 count++;
//                 $("#notification_counter").html(count);
//             }
//         },
//     });
// });
// window.Echo.private('PaymentFrequency').listen('PaymentFrequencyEvent', e => {
//     $.ajax({
//         type: "post",
//         url: '/pusher_payment_frequency',
//         data: { details: e.details },
//
//         success: function success(msg) {
//             if(msg === 'ok')
//             {
//                 var link    =   '/buyer/about/'+e.details.seller_company_id+'/seller';
//
//                 $("#notification_icon").removeClass('invisible').addClass('visible');
//                 $("#notifications").prepend(
//                     '<a class="list-group-item d-flex justify-content-between align-items-center" href="'+link+'">'
//                     + e.details.message
//                     + '</a>');
//                 count++;
//                 $("#notification_counter").html(count);
//             }
//         },
//     });
// });
// window.Echo.private('DeliveryDays').listen('DeliveryDaysEvent', e => {
//     $.ajax({
//         type: "post",
//         url: '/pusher_delivery_days',
//         data: { details: e.details },
//
//         success: function success(msg) {
//             if(msg === 'ok')
//             {
//                 var link    =    '/buyer/about/'+e.details.seller_company_id+'/seller';
//
//                 $("#notification_icon").removeClass('invisible').addClass('visible');
//                 $("#notifications").prepend(
//                     '<a class="list-group-item d-flex justify-content-between align-items-center" href="'+link+'">'
//                     + e.details.subject
//                     + '</a>');
//                 count++;
//                 $("#notification_counter").html(count);
//             }
//         },
//     });
// });

