var selected_currencies =   [];
$(document).on("click", ".preferred_currency", function (e) {
    $('.preferred_currency').removeClass('text-orange').addClass('text-success');
    var curr    =   $(this).data('currency');
  $('#'+curr+'_label').addClass('text-orange');

});
$(document).on("click", ".seifex_currency", function (e) {

    var currency = $(this).val();

    if($('.' +currency).is(":checked"))
    {

        if(jQuery.inArray(currency, selected_currencies) === -1)
        {
            selected_currencies.push(currency);

            $('.'+currency).addClass('text-orange');

            $('#preferred_currency').append(
                '<div id="'+ currency + '" >'+
                '<input type="radio"  name ="preferred_currency" data-currency="'+currency+'"  required class="form-check-input preferred_currency " value = '+ currency + ' />'+
                ' <label for="preferred_currency" id="'+currency+'_label'+'" class="form-check-label preferred_currency text-success">'+ $(this).data('display') + '</label>'
                +'</div>'
            );
        }

    }
    else if(!$('.' + currency).is(":checked") )
    {
        swal.fire('your price lists in '+currency+' will be disabled !');
        $('.'+currency).removeClass('text-orange').addClass('text-success');
        document.getElementById(currency).remove();
        selected_currencies.splice(selected_currencies.indexOf(currency), 1);
    }

});
$(document).on("click", ".more_currencies", function (e) {

    /*span to load neighbour currencies*/
    $('#more_currencies').addClass('d-none');
    /*span to toggle neighbour currencies*/
    $('.neighbour_currencies').removeClass('d-none');
    /*span to load remaining currencies*/
    $('#remaining_curr').removeClass('d-none');

    var  country_id = $(this).data('country_id');

    $.ajax({
        type: "post",
        url: '/neighbour_currencies',
        data: {
            country_id:country_id, who: $(this).data('who')
        },
        success: function success(msg) {

            $('#'+msg['who'].split('_')[1]+'_currencies').show().load('/'+msg['who']+'_currencies?country_id='+country_id);

            if(msg['who'].split('_')[1] === 'remaining')
            {
                /*span to load remaining currencies*/
                $('#remaining_curr').addClass('d-none');
                /*span to toggle remaining currencies*/
                $('.remaining_currencies').removeClass('d-none');

            }
        }
    });
});
///// TOGGLE CURRENCIES DIV
$(document).on("click", ".toggle_currencies_div", function (e){

    $('#'+$(this).data('div')).slideToggle();

    /*span to load neighbour currencies*/
    $('#more_currencies').addClass('d-none');

    /*span to toggle neighbour currencies*/
    $('#neighbour_currencies').removeClass('d-none');

    if($(this).attr('id') === 'remaining_curr')
    /*span to toggle remaining currencies*/
        $('.remaining_currencies').removeClass('d-none');

});
