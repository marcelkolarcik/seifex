$(document).on("click",".duties", function (e) {

    e.preventDefault();
    $('#new_staff_form').html('');
    $('#scope_form').html('');
    $('#transfer_companies').html('');

    var staff_id    =   $(this).data('staff_id');
    var staff_position    =   $(this).data('staff_position');
    var staff_role    =   $(this).data('role');
    var staff_hash    =   $(this).data('staff_hash');

    $('#duties_form').load('/duties', { staff_id:staff_id,staff_position:staff_position,staff_role:staff_role,staff_hash:staff_hash});

});
$(document).on("click",".scope", function (e) {

    e.preventDefault();
    $('#new_staff_form').html('');
    $('#duties_form').html('');
    $('#transfer_companies').html('');
    var staff_id    =   $(this).data('staff_id');

    var staff_position    =   $(this).data('staff_position');
    var role    =   $(this).data('role');
    var work_scopes_staff_id    =   $(this).data('work_scopes_staff_id');
    var manager_delegation_id    =   $(this).data('delegation_id');
    var phone_number    =   $(this).data('phone_number');
    var staff_hash    =   $(this).data('staff_hash');


   /*if adding scope for manager for the first time*/
    if(staff_position   === 'manager' && work_scopes_staff_id==='')
    {
        $('#scope_form').load('/create_staff', { staff_id:staff_id,staff_position:staff_position,role:role,
            manager_delegation_id:manager_delegation_id,phone_number:phone_number});
    }
    else
    {
        $('#scope_form').load('/edit_staff', { staff_id:staff_id,staff_position:staff_position,staff_hash:staff_hash,role:role});
    }


});
$(document).on("click",".staff_link", function (e) {

    e.preventDefault();
    $('#scope_form').html('');
    $('#duties_form').html('');
    $('#new_staff_form').html('');
});

$(document).on("click",".add_staff", function (e) {

    e.preventDefault();
    $('#duties_form').html('');
    $('#transfer_companies').html('');
    $('#scope_form').html('');
    var staff_role    =   $(this).data('staff_role');
    var staff_position    =   $(this).data('staff_position');
    $('#new_staff_form').load('/create_staff', { staff_role:staff_role,staff_position:staff_position});



});
$(document).on("click",".undelegate_staff", function (e){

   e.preventDefault();
    var staff_role   =   $(this).data('staff_role');
    var staff_email   =   $(this).data('staff_email');
    var staff_position   =   $(this).data('staff_position');
    var company_name_desc   =   $(this).data('company_name_desc');
    var company_name     = $(this).data('company_name');



    var title = $(this).attr('title');
    var text = $(this).data('text');
    var wrong = $(this).data('wrong');
    var later  = $(this).data('later');


    // $a = "hello";
    // $['hello'] = 'world';
    // alert( $[$a])
    swal.fire({

        title: title,
        text: text,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'limegreen',
        cancelButtonColor: 'red',
        confirmButtonText:
            '&#10004;',
        confirmButtonAriaLabel: 'Confirm.',
        cancelButtonText:
            '&#10060;',
        cancelButtonAriaLabel: 'Cancel',
    }).then(function (result) {
        if (result.value) {

            $.ajax({
                type: "post",
                url: '/undelegate_staff',
                data: {
                    staff_role: staff_role,
                    staff_email: staff_email,
                    staff_position: staff_position,
                    company_name_desc: company_name_desc,
                    company_name:company_name },

                success: function success(msg) {
                    if(msg['status'] === 'deleted' )
                        swal.fire({
                            position: 'top-end',
                            type: 'success',
                           /* title: msg['text'] ,*/
                            showConfirmButton: false,
                            timer: 1500
                        });

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
/*CLICK ON COUNTY_L4*/
$(document).on("change",".two", function (e) {

    var location_name       =   $(this).data('location_name');
    var parent_level_div    =   $(this).data('parent_level_div');
    var location_id         =   hashCode(location_name);

    if($(this).is(":checked")) {
        $('#location_name'+parent_level_div).addClass('small text-grey-500')
        $('#new_staff'+parent_level_div).append('<div class="ml-3" id="'+location_id+'"><h5 id="location_name'+location_id+'">' + location_name + '</h5></div>');
    }
    else
    {

        if( $('#new_staff'+parent_level_div).contents().length === 2)
        {
            $('#location_name'+parent_level_div).removeClass('small text-grey-500')
        }


        $('#'+location_id).remove()
    }
});
/*CLICK ON COUNTY*/
$(document).on("change",".one", function (e) {
    e.preventDefault();
    var base_location    =   $(this).val();
    var location_name   =   $(this).data('location_name');

    var parent_div      =   $(this).data('parent_div');
    var location_id     =   hashCode(location_name);


        // ...


    if(this.checked)
    {

        $('#location_name'+parent_div).addClass('small text-grey-500')
        $.ajax({
            type: "get",
            url: '/display_counties_4',
            data: {selected_county: base_location},
            success: function success(msg) {
                $('.county_level_4').addClass('small text-grey-500');
                if (msg['end'] !== 'end') {

                    $('#parent_div'+parent_div).prepend('<div class="bg-secondary text-light mb-3" id="' + location_id + '">' +
                        '<span  class="pl-2">' +'&#11165;  '+ location_name +'  &#11165; '+ '</span><br/></div>');

                    for (i = 0; i < msg[0].length; i++) {

                        $('#parent_div'+parent_div).prepend(
                            '<span class=" '+location_id+' ">' +
                                '<input ' +
                                'name="counties_l4[' + base_location + '][]" ' +
                                'type="checkbox" ' +

                                'data-location_name="' + msg[0][i] + '" ' +
                                'data-parent_level_div="' + location_id + '" ' +
                                'data-parent_div="' + parent_div + '" ' +
                                'data-base_location="' + base_location + '" ' +
                                'class="two" ' +
                                'value= ' + msg[1][i] + ' /> '

                                + msg[0][i] + '<br />' +
                            '</span>');
                    }

                }

            }
        })

        $('#new_staff'+parent_div).append('<div class="ml-4"  id="' + 'new_staff'+location_id + '"><h5 id="location_name'+location_id+'">' + location_name + '</h5></div>');
      //  $('#location_name'+location_id).addClass('d-none')
    }
    else
    {
        if( $('#new_staff'+parent_div).contents().length === 2)
        {
            $('#location_name'+parent_div).removeClass('small text-grey-500');

        }

        $('#'+location_id).remove();
        $('.'+location_id).remove();
      $('#new_staff'+location_id).remove();
    }
});
/*CLICK ON BASE LOCATION*/
$(document).on("change",".base_location", function (e) {

   // e.preventDefault();

    var base_location       =   $(this).val();
    var location_name       =   $(this).data('location_name');
    var location_id         =   hashCode(location_name);

    var count               =   level(base_location,'.');

    $('.included_locations').removeClass('d-none');
    if($(this).is(":checked")) {
        /*country*/
        if (count === 1) {
            var selected_country = base_location;
            $.ajax({
                type: "get",
                url: '/display_counties',
                data: {selected_country: selected_country},
                success: function success(msg) {
                    $('.county_level_4').addClass('small text-grey-500');
                    if (msg['end'] !== 'end') {

                        $('#load').prepend('<div  id="' + 'parent_div'+location_id + '">' +
                            '<div class="bg-secondary text-light">' +
                            '<span  class="pl-2">' + location_name + '</span>' +
                            '<br/>' +
                            '</div>'+
                            '</div>' +
                            '<span class=" '+location_id+' ">' +
                             '<br />'

                           );

                        for (i = 0; i < msg['county_names'].length; i++) {

                            $('#parent_div'+location_id).append('' +
                                '<span class=" '+location_id+' ">' +
                                    '<input name="counties[' + base_location + '][]" ' +
                                    'type="checkbox" ' +
                                    'class="one" ' +

                                    'data-parent_div="' + location_id + '" ' +
                                    'data-location_name="' + msg['county_names'][i] + '" ' +
                                    'value=' + msg['county_ids'][i] + ' /> '

                                    + msg['county_names'][i] + '<br />' +

                                '</span>');
                        }
                        $('#parent_div'+location_id).append('<div class="bg-secondary mb-3">&nbsp;</div>'+
                            '</span>' +
                            '<span class=" '+location_id+' ">' +
                            '<input name="countries[' + base_location + '][]" ' +
                            'type="checkbox" ' +
                            'checked="checked"'+
                            'class="" ' +
                            'value="'+base_location+'" ' +
                            'data-parent_div="' + location_id + '" '
                            + ' /> '

                            +base_location + '<br />' +

                            '</span>');
                    }
                }
            })
            $('#new_staff').prepend('<div type="text" class="card border-grey-300 mb-3 " id="' + 'new_staff'+location_id + '">' +
                '<h5 id="location_name'+location_id+'"  class="pl-2 ">' + location_name + '</h5></div>');
        }
        /*county*/
        else if (count === 2) {
            var selected_county = base_location;

            $.ajax({
                type: "get",
                url: '/display_counties_4',
                data: {selected_county: selected_county},
                success: function success(msg) {
                    $('.county_level_4').addClass('small text-grey-500');
                    if (msg['end'] !== 'end') {

                        $('#load').prepend('<div  id="' + 'parent_div'+location_id + '">' +
                            '<div class="bg-secondary text-light">' +
                            '<span class="pl-2">' + location_name + '</span>' +
                            '</div>'+
                            '<br/></div>');

                        for (i = 0; i < msg[0].length; i++) {

                            $('#parent_div'+location_id).append('' +
                                '<span class=" '+location_id+' ">' +
                                    '<input ' +
                                    'name="counties_l4[' + base_location + '][]" ' +
                                    'type="checkbox" ' +
                                    'class="one" ' +
                                    'data-parent_div="' + location_id + '" ' +
                                    'data-location_name="' + msg[0][i] + '" ' +
                                    'value= ' + msg[1][i] + ' /> '

                                    + msg[0][i] + '<br />' +

                                '</span>');
                        }
                        $('#parent_div'+location_id).append('<div class="bg-secondary mb-3">&nbsp;</div>'+
                            '</span>' +
                            '<span class=" '+location_id+' ">' +
                            '<input name="counties[' + base_location + '][]" ' +
                            'type="checkbox" ' +
                            'checked="checked"'+
                            'class="" ' +
                            'value="'+base_location+'" ' +
                            'data-parent_div="' + location_id + '" '
                            + ' /> '

                            +base_location + '<br />' +

                            '</span>');
                    }

                }
            })
            $('#new_staff').prepend('<div type="text" class="card border-grey-300  mb-3 " id="' + 'new_staff'+location_id + '">' +
                '<h5 class="pl-2 "  id="location_name'+location_id+'">' + location_name + '</h5></div>');
        }
        /*county_l4*/
        else

        {
            $('#new_staff').prepend('<div type="text" class="card border-grey-300  mb-3 " id="' + 'new_staff'+location_id + '">' +
                '<h5 class="pl-2 "  id="location_name'+location_id+'" >' + location_name + '</h5></div>');

            $('#new_staff'+location_id).append('<div class="bg-secondary mb-3">&nbsp;</div>'+
                '</span>' +
                '<span class=" '+location_id+' ">' +
                '<input name="counties_l4[' + base_location + '][]" ' +
                'type="checkbox" ' +
                'checked="checked"'+
                'class="" ' +
                'value="'+base_location+'" ' +
                'data-parent_div="' + location_id + '" '
                + ' /> '

                +base_location + '<br />' +

                '</span>');
        }

    }
    else
    {
        $('#'+'parent_div'+location_id).remove();
        $('.'+location_id).remove();

        $('#new_staff'+location_id).remove();

    }

});

$(document).on("click",".delete_location", function (e) {
    e.preventDefault();
    var location_id = $(this).data('location_id');

    $('.'+location_id).remove();

});

function hashCode (str){
    var hash = 0;
    if (str.length == 0) return hash;
    for (i = 0; i < str.length; i++) {
        char = str.charCodeAt(i);
        hash = ((hash<<5)-hash)+char;
        hash = hash & hash; // Convert to 32bit integer
    }
    return hash;
}
function level(string, subString, allowOverlapping) {

    string += "";
    subString += "";
    if (subString.length <= 0) return (string.length + 1);

    var n = 0,
        pos = 0,
        step = allowOverlapping ? 1 : subString.length;

    while (true) {
        pos = string.indexOf(subString, pos);
        if (pos >= 0) {
            ++n;
            pos += step;
        } else break;
    }

    return n;
}
