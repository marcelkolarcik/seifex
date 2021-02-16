var selected_languages =   [];
$(document).on("click", ".preferred_language", function (e) {
    $('.preferred_language').removeClass('text-orange').addClass('text-success');
    var lang    =   $(this).data('language');
    $('#'+lang+'_label').addClass('text-orange');

});

$(document).on("click", ".language", function (e) {

    var language    =   $(this).val();
    var language_l    =   $(this).data('label');
    if($(this). prop("checked") === true )
    {
        if(jQuery.inArray(language, selected_languages) === -1)
        {
            selected_languages.push(language);
        $('.'+language).addClass('text-orange');

            if($('.language_l').length >= 1)
            {

                $('#pref_lang_wrapper ').removeClass('d-none');
                $('#preferred_languages').append(
                    '<div  id="'+ language + '_'+'" >'+
                    '<input type="radio"  name ="preferred_language" data-language="'+language+'" required class="preferred_language" value = '+ language + ' />'+
                    ' <label for="preferred_language" id="'+language+'_label'+'" class="form-check-label preferred_language text-success">'+'&nbsp;'+ $(this).data('label') + '</label>'
                    +'</div>'
                );
            }
        }
    }
    else if($(this). prop("checked") === false)
    {
        swal.fire('your product lists and price lists in '+language_l+' will be disabled !');
        $('.'+language).removeClass('text-orange').addClass('text-success');
        document.getElementById(language + '_').remove();
        selected_languages.splice(selected_languages.indexOf(language), 1);
    }

});

$(document).on("click", ".more_languages", function (e) {

    /*span to load neighbour languages*/
    $('.more_languages').addClass('d-none');
    /*span to toggle neighbour languages*/
    $('.neighbour_languages').removeClass('d-none');

    /*span to load remaining languages*/
    $('#remaining_langs').removeClass('d-none');

    var  country_id = $(this).data('country_id');

    $.ajax({
        type: "post",
        url: '/neighbour_languages',
        data: {
            country_id:country_id, who: $(this).data('who')
        },
        success: function success(msg) {

            $('#'+msg['who'].split('_')[1]+'_languages').show().load('/'+msg['who']+'_languages?country_id='+country_id);

            if(msg['who'].split('_')[1] === 'remaining')
            {

                /*span to load remaining languages*/
                $('#remaining_langs').addClass('d-none');
                /*span to toggle remaining languages*/
                $('.remaining_languages').removeClass('d-none');
            }

        }
    });
});
////// TOGGLE LANGUAGES DIV
$(document).on("click", ".toggle_languages_div", function (e){

    $('#'+$(this).data('div')).slideToggle();

    /*span to load neighbour languages*/
    $('#more_languages').addClass('d-none');

    /*span to toggle neighbour languages*/
    $('#neighbour_languages').removeClass('d-none');


    if($(this).attr('id') === 'remaining_langs')
    /*span to toggle remaining languages*/
        $('.remaining_languages').removeClass('d-none');

});
