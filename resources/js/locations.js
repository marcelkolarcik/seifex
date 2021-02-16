$(document).on("change", "#country", function (e) {
    e.preventDefault();
    var optionSelected = $(this).find("option:selected");
    var selected_country = optionSelected.val();
    var wrong  = $(this).attr('wrong');
    var later  = $(this).attr('later');

    $('.county').addClass('d-none');
    $('.county_level_4').addClass('d-none');
    $('.submit_button').addClass('d-none');
    $('#old_counties').remove();
    ///// button to load more (neighbour languages)
    $('#more_languages').data('country_id',selected_country);


    $.ajax({
        type: "get",
        url: '/display_counties',
        data: { selected_country: selected_country },
        success: function success(msg) {

            if (msg['end'] !== 'end') {

                $('.county').removeClass('d-none');

                $('#county').find('option').remove().end();

                for (i = 0; i < msg['county_names'].length; i++) {
                    $('#county').append($('<option>', {
                        value: msg['county_ids' ][i],
                        text: msg['county_names'][i]
                    }));
                }


                $("#county").prop("selectedIndex", -1);

               /* $('.currencies').removeClass('d-none');*/
                $('#currently_selected').removeClass('d-none');
                /*selected country currency*/
                $('.new_currency').html(msg['selected_currency'].replace("_", " "));
                // $('.preferred_currency').html(msg[2]).val(msg[2]);
              /*  $('.preferred_currency').val(msg[2]);*/

                /*load neighbour currencies*/
                $('#more_currencies').removeClass('d-none');
                /*load remaining currencies*/
                $('#remaining_curr').addClass('d-none');
                /*span to toggle remaining currencies*/
                $('.remaining_currencies').addClass('d-none');
                /*span to toggle neighbour currencies*/
                $('.neighbour_currencies').addClass('d-none');
                /*div for neighbour currencies*/
                $("#neighbour_currencies").html('');
                /*div for remaining currencies*/
                $("#remaining_currencies").html('');






                if(msg['creating_company'] === true)
                {
                    $('#pref_lang_wrapper ').addClass('d-none');

                    /*div for preferred languages*/
                    $("#preferred_languages").html('');

                    /*div for preferred currencies*/
                    $("#preferred_currency").html('');

                }

                /*span for load neighbour languages*/
                $('#more_languages').removeClass('d-none');
                /*load remaining currencies*/
                $('#remaining_langs').addClass('d-none');
                /*span to toggle remaining currencies*/
                $('.remaining_languages').addClass('d-none');
                /*span to toggle neighbour currencies*/
                $('.neighbour_languages').addClass('d-none');

                /*div for neighbour languages*/
                $("#neighbour_languages").html('');
                /*div for remaining languages*/
                $("#remaining_languages").html('');

                /*div for missing country languages*/
                $("#missing_country_languages").html('');
                /*div for missing country currency*/
                $("#missing_country_currency").html('');

                $("#languages").html('');

                $('.languages_wrapper').removeClass('d-none');
                $('.currency_wrapper').removeClass('d-none');

                for (i = 0; i < msg['new_languages'].length; i++) {

                    var val =  msg['new_languages'][i].split('|')[0];
                    var label =  msg['new_languages'][i].split('|')[1];


                    $("#languages").append (

                        "<input class='language language_l' data-label='"+label+"'  name='languages[]' id='" + val + "' type='checkbox' value='" + val + "' />"+
                       "&nbsp;"+ "<label class='text-success "+val+"' for='" + val + "'>" + label + "</label>" + "&nbsp;");
                };

                   //var country_currency =  msg['selected_currency'];
                   var country_currency = msg['country_currency'];

                   if(country_currency !== null)
                   {
                       $("#currencies").html('').append (

                           "<input class='seifex_currency  "+country_currency
                           +"' data-display='"+msg['selected_currency'].replace("_", " ")
                           +"' data-label='"+country_currency
                           +"'  name='currencies[]' " +
                           "type='checkbox' " +
                           "value='" + country_currency + "' />"
                           +"&nbsp;"
                           + "<label class='text-success "+country_currency+"' for='" + country_currency + "'>"
                           + msg['selected_currency'].replace("_", " ") +
                           "</label>" + "<br>");
                   }
                   else {
                       $("#currencies").html('');
                   }



            } else if (msg['end'] === 'end') {
                $('.submit_button').removeClass('d-none');
                $('.currencies').removeClass('d-none');
                $('.new_currency').html(msg['country_currency']);
                $('#remaining_curr').addClass('d-none');

                $('input.new_currency')/*.prop('checked', true)*/.prop('value', msg['country_currency']);
            } else {
                swal.fire({
                    title: wrong,
                    text: later,
                    type: "warning",
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
                showConfirmButton: false,
                timer: 2500
            });
        }
    });
});



$(document).on("change", "#county", function (e) {
    e.preventDefault();
    var countySelected = $(this).find("option:selected");
    var selected_county = countySelected.val();
    var wrong  = $(this).attr('wrong');
    var later  = $(this).attr('later');

    $.ajax({
        type: "get",
        url: '/display_counties_4',
        data: { selected_county: selected_county },
        success: function success(msg) {

            if (msg['end'] !== 'end') {

                $('.county_level_4').removeClass('d-none');
                $('#county_level_4').find('option').remove().end();
                for (i = 0; i < msg[0].length; i++) {
                    $('#county_level_4').append($('<option>', {
                        value: msg[1][i],
                        text: msg[0][i]
                    }));
                }
                $("#county_level_4").prop("selectedIndex", -1);
            } else if (msg['end'] === 'end') {
                $('.submit_button').removeClass('d-none');
                $('.county_level_4').addClass('d-none');
            } else {
                swal.fire({
                    title: wrong,
                    text: later,
                    type: "error",
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
                showConfirmButton: false,
                timer: 2500

            });
        }
    });
});

$(document).on("change", "#county_level_4", function (e) {
    e.preventDefault();
    $('.submit_button').removeClass('d-none');
});
