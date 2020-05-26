
var slug = function(str) {
    var trimmed = $.trim(str);
    var $slug = trimmed.replace(/[^a-z0-9-]/gi, '-').
    replace(/-+/g, '-').
    replace(/^-|-$/g, '');
    return $slug.toLowerCase();
}

function addProgressList(form){

    $(form).children(".response").remove();

    $(form).append('<div class="response small" id="response_form_'+$(form).attr('id')+'"></div>').children(".response").append('<div class="progress" id="progress"><div class="bar" id="bar"></div><div class="percent" id="percent">0%</div></div>');


}


$(document).ready(function() {
	$('.deftitle').keyup(function() {
		$('#slug').val(slug($('.deftitle').val()));
	});

    $(".frmChangeOrder").each(function(){
           var $form = $(this);
            console.log($form);
        $($($form)).ajaxForm({
            beforeSubmit: function() {
                if (!$($form).valid()) {return false;}

                addProgressList($form);


            },
            beforeSerialize:function($tForm, options){

                return true;
            },
            beforeSend: function() {
                if (!$($form).valid()) {return false;};
                $($form).find('#status').html("");
                var percentVal = '0%';
                $($form).find('#bar').css("width",percentVal);
                $($form).find('#percent').html(percentVal);
            },
            uploadProgress: function(event, position, total, percentComplete) {
                var percentVal = percentComplete + '%';
                $($form).find('#bar').css("width",percentVal);
                $($form).find('#percent').html(percentVal);
            },
            success: function() {
                var percentVal = '100%';
                $($form).find('#bar').css("width",percentVal);
                $($form).find('#percent').html(percentVal);
            },
            complete: function(xhr) {
                var datos  =xhr.responseText;
                var jsondata = $.parseJSON(datos);
                if(jsondata.error==0) {
                    $.notify(jsondata.msg, "success")


                    if(jsondata.redirect) setTimeout(function() {window.location.href=jsondata.redirect;	},2000);
                }
                else {
                    var html = jsondata.msg;
                    if(jsondata.errors && jsondata.errors.length>0) {
                        html+='<ul>';
                        $.each(jsondata.errors, function(k,error) {
                            html+='<li>'+error+'</li>';
                        });
                        html+='</ul>';
                    }
                    html+='</div>';

                    $.notify(html, "warn");
                }
                $('.loader-overlay').delay(2000).fadeOut(300);
            }
        });

    });



});




$(document).ready(function() {
    // multiple elements



});
