/**
 * Created by Alex on 20/03/2017.
 */
$(document).ready(function() {
    load_switcher_click();
    var MENU_URL = $('html').attr('MENU_URL');
   // buttons_more_minus();
});
$(document).ready(function(){
    $(".owl-carousel").owlCarousel({
        nav:true,
        nav:true,
    });
});
function load_switcher_click(){
    /* FUNCTION COLOR SWITCH */
    $('.color-switcher').click(function() {
        $('.color-switcher').removeClass('checked');
        var combid = $(this).attr('combination');
        var product = $(this).attr('product');
        $('.product-main-img').hide(0);
        $(product+' .main-listed, .img-combination').slideUp(0);
        $(combid).fadeIn('300');
        $(this).addClass('checked');

        var idcombination = $(this).attr('idcombination');
        var idproduct = $(this).attr('idproduct');

        $.ajax({
            data: {idcombination:idcombination} ,
            type: "POST",
            dataType: "json",
            url: "/product/getQuickCart",
        }).done(function( data, textStatus, jqXHR ) {
            if ( console && console.log ) {
                var options = '';
                $.each(data, function(i, val) {
                    var acabado= '';
                    if (val.acabado !=null)  acabado= ' - '+val.acabado;
                    options += '<option value="'+val.id+'" price="'+val.price+'">'+val.ancho+' x '+val.alto+'cm '+val.acabado+'</option>';
                });

                $('#actions-prod-'+idproduct).html('' +
                    '<div class="col-sm-5">' +
                    '<select  class="select-medidas" prchange="#pr'+idproduct+'" id="medidas-'+idproduct+'">' +
                     options +
                    '</select>' +
                    '</div>' +
                    '<div class="col-sm-3"><span class="precio-show" id="pr'+idproduct+'"><i class="fa fa-chevron-left"></i></span></div>' +
                    '<div class="col-sm-4"> ' +
                    '<a href="#0"  data-qty="1" class="cd-add-to-cart" data-id="'+idproduct+'" class="btn-prod"">AÑADIR</i></a>' +
                    '</div>');

                $('.select-medidas').change(function(){
                    var tochange=$(this).attr('prchange');
                    var $option_selected = $(this).find('option:selected');

                    if(!$option_selected.attr('price')){$(tochange).html('<i class="fa fa-chevron-left"></i>');}
                    else { $(tochange).html($option_selected.attr('price')+'€');}
                });

                $('.select-medidas').change();

            }
        }).fail(function( jqXHR, textStatus, errorThrown ) {
            if ( console && console.log ) {
                console.log( "La solicitud ha fallado: " +  textStatus);
            }
        });


    });
}