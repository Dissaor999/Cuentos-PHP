var slug = function(str) {
    var $slug = '';
    var trimmed = $.trim(str);
    $slug = trimmed.replace(/[^a-z0-9-]/gi, '-').
    replace(/-+/g, '-').
    replace(/^-|-$/g, '');
    return $slug.toLowerCase();
}
$(document).ready(function() {
	$('.deftitle').keyup(function() {
		
		$('#slug').val(slug($('.deftitle').val()));
	});





});

//== Class definition

var BootstrapDatetimepicker = function () {

    //== Private functions
    var demos = function () {
        // minimal setup
        $('#m_datetimepicker_1').datetimepicker({
            todayHighlight: true,
            autoclose: true,
            format: 'yyyy.mm.dd hh:ii'
        });

        $('#m_datetimepicker_1_modal').datetimepicker({
            todayHighlight: true,
            autoclose: true,
            format: 'yyyy.mm.dd hh:ii'
        });

        // input group demo
        $('#m_datetimepicker_2, #m_datetimepicker_1_validate, #m_datetimepicker_2_validate, #m_datetimepicker_3_validate').datetimepicker({
            todayHighlight: true,
            autoclose: true,
            pickerPosition: 'bottom-left',
            format: 'yyyy/mm/dd hh:ii'
        });
        $('#m_datetimepicker_2_modal').datetimepicker({
            todayHighlight: true,
            autoclose: true,
            pickerPosition: 'bottom-left',
            format: 'yyyy/mm/dd hh:ii'
        });

        // today button
        $('#m_datetimepicker_3').datetimepicker({
            todayHighlight: true,
            autoclose: true,
            pickerPosition: 'bottom-left',
            todayBtn: true,
            format: 'yyyy/mm/dd hh:ii'
        });
        $('#m_datetimepicker_3_modal').datetimepicker({
            todayHighlight: true,
            autoclose: true,
            pickerPosition: 'bottom-left',
            todayBtn: true,
            format: 'yyyy/mm/dd hh:ii'
        });

        // orientation
        $('#m_datetimepicker_4_1').datetimepicker({
            todayHighlight: true,
            autoclose: true,
            pickerPosition: 'bottom-left',
            format: 'yyyy.mm.dd hh:ii'
        });

        $('#m_datetimepicker_4_2').datetimepicker({
            todayHighlight: true,
            autoclose: true,
            pickerPosition: 'bottom-right',
            format: 'yyyy/mm/dd hh:ii'
        });

        $('#m_datetimepicker_4_3').datetimepicker({
            todayHighlight: true,
            autoclose: true,
            pickerPosition: 'top-left',
            format: 'yyyy-mm-dd hh:ii'
        });

        $('#m_datetimepicker_4_4').datetimepicker({
            todayHighlight: true,
            autoclose: true,
            pickerPosition: 'top-right',
            format: 'yyyy-mm-dd hh:ii'
        });

        $('#m_datetimepicker_5').datetimepicker({
            format: "dd MM yyyy - HH:ii P",
            showMeridian: true,
            todayHighlight: true,
            autoclose: true,
            pickerPosition: 'bottom-left'
        });

        $('#m_datetimepicker_6').datetimepicker({
            format: "yyyy/mm/dd",
            todayHighlight: true,
            autoclose: true,
            startView: 2,
            minView: 2,
            forceParse: 0,
            pickerPosition: 'bottom-left'
        });

        $('#m_datetimepicker_7').datetimepicker({
            format: "hh:ii",
            showMeridian: true,
            todayHighlight: true,
            autoclose: true,
            startView: 1,
            minView: 0,
            maxView: 1,
            forceParse: 0,
            pickerPosition: 'bottom-left'
        });
    }

    return {
        // public functions
        init: function() {
            demos();
        }
    };
}();

jQuery(document).ready(function() {
    BootstrapDatetimepicker.init();
});


//== Class definition

var SummernoteDemo = function () {
    //== Private functions
    var demos = function () {
        $('.summernote').summernote({
            height: 450,

            lang: 'es-ES',

            onImageUpload: function(files, editor, $editable) {
                sendFile(files[0],editor,$editable);
            }
        });

        function sendFile(file,editor,welEditable) {
            data = new FormData();
            data.append("file", file);
            $.ajax({
                url: '/admin/blog/uploader',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
                success: function(data){
                    alert(data);
                    $('.summernote').summernote("insertImage", data, 'filename');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus+" "+errorThrown);
                }
            });
        }


    }

    return {
        // public functions
        init: function() {
            demos();
        }
    };
}();

//== Initialization
jQuery(document).ready(function() {
    SummernoteDemo.init();
});