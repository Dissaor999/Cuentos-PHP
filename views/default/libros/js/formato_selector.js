/**
 * Created by Alex on 27/12/2017.
 */
$(document).ready(function() {
    $('.method').eq(0).addClass('active');

    $('.method').on('click', function(){
        var $method = $(this);
        $('input', $method).prop('checked', true);

        $('.method').removeClass('active');
        $method.addClass('active');
    });
});