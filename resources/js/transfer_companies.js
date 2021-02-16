/*loading form*/
$(document).on("click",".transfer_companies", function (e) {
    e.preventDefault();
    $('#duties_form').html('');
    $('#scope_form').html('');
    var staff_role    =   $(this).data('staff_role');
    $('#transfer_companies').load('/companies/transfer/form', { staff_role:staff_role});

});

/*unchecking companies that belong to the same seller*/
$(document).on("change",".to_seller", function (e) {
    e.preventDefault();
    var seller_id    =   $(this).val();
    $("."+seller_id).prop("checked", false);
});

