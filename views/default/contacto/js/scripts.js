/**
 * Created by Alex on 20/03/2017.
 */
$(document).ready(function() {


    // this is the id of the form
    $(".ajax-form").submit(function(e) {
        $form = $(this);
        var url = $form.attr('action'); // the script where you handle the form input.
        $form.children('.response').remove();
        $.ajax({
            type: "POST",
            url: url,
            data: $form.serialize(), // serializes the form's elements.
            success: function(json)  {
                data=jQuery.parseJSON(json);
                if(data['success']>0) {
                    $form.append('<ul class="response alert alert-success"></ul>');
                    $.each(data['messages'], function(i,message) {
                        $form.children('.response').append('<li>- '+message+'</li>');
                    });
                    $form[0].reset();
                }
                else {
                    $form.append('<ul class="response alert alert-danger"></ul>');
                    $.each(data['errors'], function(i,message) {
                        $form.children('.response').append('<li>- '+message+'</li>');
                    });


                }
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });


});
