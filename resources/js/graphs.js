import Chart from 'chart.js'
function draw_graph(data,type,options)
{
    /*replacing unwanted chars from location strings*/
    data = JSON.stringify(data);
    data = data.replace(/\--/g, ' | ').replace(/\-/g, ' ').replace(/\?/g, ' - ').replace(/\_/g, ' ');
    data = JSON.parse(data);

    /*replacing underscore from location title*/
    options = JSON.stringify(options);
    options = options.replace(/\_/g, ' ');
    options = JSON.parse(options);


    var context = $('#graph');

    new Chart(context, {
        type: type,
        data: data,
        options:options

    });
}
if(window.location.pathname === '/charts')
{
    var graph = $('#graph');
    var data =graph.data('data');
    var type =  graph.data('type');
    var options =  graph.data('options');

    draw_graph(data,type,options);
    $(document).on("click", ".stats", function (e) {

        e.preventDefault();
        $(".graph_holder").show();
        var period = $(this).data('period');
        var stats_by = $(this).data('by');
        var year = $(this).data('year');
        var top_products = $(this).data('top_products');


        $("#figures").html('');
        $(".figure_print").addClass('d-none');
        /*to prevent old graph on hover*/
        $("canvas#graph").remove();
        $("div#stats").append('<canvas id="graph"></canvas>');
        /*to prevent old graph on hover*/

        $.ajax({
            type: "post",
            url: '/chart',
            data: { stats_by : stats_by, year : year,top_products :top_products,period:period},

            success: function success(msg) {

                $('ul#perc').remove();
                $('.figures').remove();

                var list = '';
                var figures = 'FULL FIGURES';

                $.each(msg['percentage'], function (name, sum) {

                    list +=  '<li class="list-group-item p-1  d-flex justify-content-between ' +
                        'align-items-center border-top-0 border-left-0 border-right-0 mb-1">' +
                        '<span  style="background-color:'+ msg['percentage_colors'][name] +'" >&nbsp;&nbsp;</span><span>'
                        +  name +'</span><span class="bg-secondary text-grey-300 pl-2 pr-2">' + sum + '</span>' +
                        '</li>'
                });

                $('#percentage').append(
                    '<ul id="perc" class="list-group pt-3">' + list +'</ul>'
                    );
                if(msg['more_percentage'] === true)
                {

                    $('#percentage').append('  <a class="figures list-group-item list-group-item-primary list-group-item-action pt-1 pb-1 mt-1 bg-light text-secondary "' +
                        '                               data-by='+ stats_by +
                        '                               href=""' +
                        '                               data-year = '+ year +  '>' +

                        figures
                    );
                }

                var data =msg['data'];
                var type =  msg['type'];
                var options =  msg['options'];

                draw_graph(data,type,options);
            },
            error: function error(msg) {
                swal.fire({
                    type: "error",
                    showConfirmButton: false,
                    timer: 1500
                });

            }
        });
    });

    $(document).on("click", ".figures", function (e) {

        e.preventDefault();
        var stats_by = $(this).data('by');
        var year = $(this).data('year');

        $(".graph_holder").hide();
        $(".figure_print").removeClass('d-none');
        $.ajax({
            type: "post",
            url: '/figures',
            data: { stats_by : stats_by, year : year},

            success: function success(msg) {

                $('ul#figures_list').remove();

                var list = '';

                $.each(msg, function (name, sum) {

                    list +=  '<li class="list-group-item p-1  d-flex justify-content-between ' +
                        'align-items-center border-top-0 border-left-0 border-right-0 mb-1">' +
                        '<span>'
                        +  name +'</span><span class="bg-secondary text-grey-300 pl-2 pr-2">' + sum + '</span>' +
                        '</li>'
                });

                $('#figures').append(
                    '<ul id="figures_list" class="list-group pt-3">' + list +'</ul>');


            },
            error: function error(msg) {
                swal.fire({
                    type: "error",
                    showConfirmButton: false,
                    timer: 1500
                });

            }
        });
    });

    $(document).on("change", "#year", function (e) {

        e.preventDefault();
        var optionSelected = $(this).find("option:selected");
        var selected_year = optionSelected.val();
        $('.stats').data('year',selected_year);

    });
    $(document).on("change", "#top_products", function (e) {

        e.preventDefault();
        var optionSelected = $(this).find("option:selected");
        var selected_top = optionSelected.val();
        $('.top_products').data('top_products',selected_top);

    });
    $(".top_products").click(function(){
        setTimeout(function(){
            $('.products').addClass("bg-info");
        }, 500);
        setTimeout(function(){
            $('.products').removeClass("bg-info");
        }, 500);
    });
    $(document).on("click", ".top_products", function (e) {

        e.preventDefault();

        if( $(this).data('product') === 'product')
        {

            var li = $('.products').addClass('bg-light_green');
            setTimeout(function () {
                li.removeClass('bg-light_green');
            }, 200);
        }





    });

}


