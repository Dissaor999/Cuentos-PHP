/**
 * Created by Alex on 31/07/2018.
 */
$('#donacion_ong').on('change', function() {
    if( this.value == 'other' ) {
        $('#donacion_ong').parent().append('<input type="text" name="order[other_ong]" id="other-ong" placeholder="Escribe la ONG" />');
        $('#other-ong').focus();
    }
    else{
        $('#other-ong').remove();
    }
})
