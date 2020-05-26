/**
 * Created by Alex on 05/03/2018.
 */


$(function() {
    $('#enviarImpresion').click(function() {
        $('#results').html('');
       // $('#results').html('<i class="fa fa-spinner fa-spin"></i> Cargando....');
        var html='';
        $('.check_pedido').each(function(i,v){
            if(this.checked) {
                $('#results').append('<li id="status_'+$(v).val()+'">Pedido '+$(v).val()+'.....<i class="loader fa fa-spinner fa-spin"></i></li>');
                $.ajax({
                    type: 'POST',
                    url: '/admin/pedidos/enviar_pedido_imprenta',
                    data:  {'id':$(v).val()},
                    dataType: 'json',
                    success: function (data) {

                        $('#status_'+$(v).val() +' i.loader').remove();
                        $('#status_'+$(v).val()).append(data["Estado"]);

                    }
                });
                }
            });
        });
});