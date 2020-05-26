/**
 * Created by Alex on 20/03/2017.
 */
$(document).ready(function() {

    var MENU_URL = $('html').attr('MENU_URL');

    /*HOME FILTER  PRODUCTS*/


    $('#filter_home').submit(function(e){

        e.preventDefault();
        var formId = $(this).attr('id');

        $('#products-container').html('<div class="text-center" style="min-height:300px"><i class="fa fa-spinner fa-spin"></i> cargando</div>');

        $.ajax({
                type: $('#filter_home').attr("method"),
                url: $('#filter_home').attr("action"),
                data: $('#filter_home').serialize(),
                async:true,
                cache: false,
                dataType: "json",
                global: true,
                ifModified: false,
                processData:true,
                success: function(datos) {


                    $('#products-container').html('');
                    if(datos[0].length>0) {
                    $.each(datos[0],function(i,prod) {

                        var prodct_html =
                            '<div class="col-sm-4 product-item" id="prod-' + prod.id + '">' +
                                '<div class="product-list animated "> ' +
                                        '<div class="images">' +

                                            '<div class="main-img main-listed"> ' +
                                                '<a href="product/details/'+prod.slug+'"><img src="/public/img/products/thumbs/' + prod.main_img + '" class="main-img" style="height:150px"></a> ' +
                                            '</div> ' +
                                            '<div class="images-combinations">';

                                                $.each(prod.colors, function(i, color) {
                                                    prodct_html += '<div id="combination-'+color.id+'" idcombination="'+color.id+'" class="img-combination" style="display:none;">';
                                                    if(color.images.length>0) {
                                                    $.each(color.images, function(i, image) {
                                                         prodct_html += '<a href="product/details/'+prod.slug+'"><img src="/public/img/combinations/thumbs/'+image.src_thumb+'"  class="main-img 111" style=""></a>';
                                                        return false;
                                                    });
                                                    }else {
                                                        prodct_html += '<a href="product/details/'+prod.slug+'"><img src="/public/img/products/thumbs/dummy.jpg"  class="main-img 2222" style=""></a>';

                                                    }
                                                    prodct_html += '</div>';

                                                });

                            prodct_html +=
                                            '</div>' +
                                        '</div>' +
                                '</div>' +
                                    '<div class="text-center" style="min-height: 52%;"> <a href="product/details/'+prod.slug+'"><h4 class="product__title">' + prod.name + '</h4></a></div> ' +
                                    '<div class="colors text-center" style="margin-bottom:8px">';
                                        $.each(prod.colors, function(i, color) {
                                            prodct_html += '<div class="color color-switcher" idproduct="'+prod.id+'" idcombination="'+color.id+'" style="background: '+color.hex+';" product="#prod-'+prod.id+'" combination="#combination-'+color.id+'"> <i class="fa fa-circle" style=""></i> </div>';

                                        });
                        prodct_html +=' </div> ' +
                                '<div class="row text-center actions-prod" id="actions-prod-'+prod.id+'"><small>Selecciona un color</small></div> ' +
                            '</div> ' +
                        '</div>';

                        $('#products-container').append(prodct_html).fadeIn(0);


                    });
                    }else {
                        $('#products-container').fadeOut(0).html('<div class="text-center"><h1>No hay resultados</h1><p>Aplica otros filtros para poder obtener la visualización de las vajillas que necesitas</p></div>').fadeIn();
                    }

                    load_switcher_click();
                }

            });
    });


    $('#filter_home .submit').click();

    $("#range").ionRangeSlider({
        type: "double",
        min: 0,
        max: 300,
        from: 0,
        postfix: " €/u",
        decorate_both: true
    });

});

function load_switcher_click(){
    /* FUNCTION COLOR SWITCH */
    $('.color-switcher').click(function(e) {
        e.preventDefault();
        $('.color-switcher').removeClass('checked');
        var combid = $(this).attr('combination');
        var product = $(this).attr('product');
        $(product+' .main-listed, '+product+' .img-combination').fadeOut(0).closest(combid).fadeIn(300);;

        $(this).addClass('checked');

        var idcombination = $(this).attr('idcombination');
        var idproduct = $(this).attr('idproduct');

        $.ajax({
            data: {idcombination:idcombination} ,
            type: "POST",
            dataType: "json",
            url: "product/getQuickCart",
        }).done(function( data, textStatus, jqXHR ) {
            if ( console && console.log ) {
                var options = '';
                console.log(data);
                $.each(data, function(i, val) {
                    var acabado= '';
                     if (val.acabado !=null)  acabado= ' - '+val.acabado;
                        options += '<option value="'+val.id+'" price="'+val.price+'">'+val.ancho+'x'+val.alto+'cm '+acabado+'</option>';
                });

                $('#actions-prod-'+idproduct).html('' +
                    '<div class="col-sm-5">' +
                    '<select  class="select-medidas cs-select cs-skin-border" prchange="#pr'+idproduct+'" id="medidas-'+idproduct+'">' +
                     options +
                    '</select>' +
                    '</div>' +
                    '<div class="col-sm-3"><span class="precio-show" id="pr'+idproduct+'"><i class="fa fa-chevron-left"></i></span></div>' +
                    '<div class="col-sm-4"> ' +
                    '<a href="#0"  data-qty="1" class="cd-add-to-cart" data-id="'+idproduct+'" class="btn-prod"">AÑADIR</a>' +
                    '</div>');

                $('.select-medidas').change(function(){
                    var tochange=$(this).attr('prchange');
                    var $option_selected = $(this).find('option:selected');

                    if(!$option_selected.attr('price')){$(tochange).html('<i class="fa fa-chevron-left"></i>');}
                    else { $(tochange).html($option_selected.attr('price')+'€');}
                });

                $('.select-medidas').change();

                (function() {
                    [].slice.call( document.querySelectorAll( '#medidas-'+idproduct ) ).forEach( function(el) {
                        new SelectFx(el);
                    } );
                })();

            }
        }).fail(function( jqXHR, textStatus, errorThrown ) {
            if ( console && console.log ) {
                console.log( "La solicitud ha fallado: " +  textStatus);
            }
        });

return false;
    });
}