
var slug = function(str) {
    var trimmed = $.trim(str);
    var $slug = trimmed.replace(/[^a-z0-9-]/gi, '-').
    replace(/-+/g, '-').
    replace(/^-|-$/g, '');
    return $slug.toLowerCase();
}
$(document).ready(function() {
	$('.deftitle').keyup(function() {
		$('#slug').val(slug($('.deftitle').val()));
	});
});


function preloadImage(_imgUrl, _container){
    var image = new Image();
    image.src = _imgUrl;
    image.onload = function(){
        $(_container).fadeTo(500, 1);
    };
}
function load_gallery_item() {

    if($('#galeria').length>0) {

        $('#galeria').html('');
        var id = $('#galeria').attr('id-load');
        $.ajax({
            type: 'POST',
            url: ''+BASE_URL+'admin_product/getPhotosCombination',
            data: {id:id},
            dataType: 'json',
            async:false,
            success: function(data) {

                var html = '';
                imagesLoaded( '.image-block');
                $.each(data.data, function(i,v){
                    html +='<div class=" col-md-2 img_container" id-image="'+v.id+'">\
                        <div class="image-ajax-gallery">\
                            <div class="image-block">\
                                <img src="'+BASE_URL+'public/img/combinations/thumbs/'+v.src_thumb+'" />\
                            </div>\
                            <div class="image-actions"><span class="remove"><i class="fa fa-trash-o"></i></span></div>\
                        </div>\
                    </div>';

                });
                $('#galeria').html(html);

                $('.image-actions .remove').click(function() {
                    var id  = $(this).closest('.img_container').attr('id-image');
                    if (confirm('realmente quieres eliminar esta imagen?' + id)) {
                        $.ajax({
                            type: 'POST',
                            url: ''+BASE_URL+'admin_product/deletePhotoCombiantion',
                            data: {id:id},
                            dataType: 'json', async:false,
                            success: function(data) {
                                if(data.success>0) {
                                    $('#gallery-actions').html('<p class="alert alert-success">Se ha borrado satisfactoriamente</p>').delay(10000).slideUp();
                                    load_gallery_item();
                                }else {
                                    $('#gallery-actions').html('<p class="alert alert-danger">No se ha podido borrar</p>').delay(10000).slideUp();
                                }
                            }
                        });
                    } else {
                        // Do nothing!
                    }
                });

            },
        });

    }
}


$(document).ready(function() {
    // multiple elements

    load_gallery_item();
    var posts = document.querySelectorAll('.image-block');
    imagesLoaded(posts, function () {
        console.log('load');
    });
    if ($('#uploader').length > 0) {
        var url = $('#uploader').attr('url');
        var id = $('#uploader').attr('id');
        $("#uploader").plupload({
            // General settings
            runtimes: 'html5,flash,silverlight,html4',
            url: url,

            // Maximum file size
            max_file_size: '15mb',

            chunk_size: '200mb',

            // Resize images on clientside if we can
            /*resize : {
             width : 200,
             height : 200,
             quality : 90,
             crop: true // crop to exact dimensions
             },
             */
            // Specify what files to browse for
            filters: [
                {title: "Image files", extensions: "jpg,gif,png"},

            ],

            // Rename files by clicking on their titles
            rename: true,

            // Sort files
            sortable: true,

            // Enable ability to drag'n'drop files onto the widget (currently only HTML5 supports that)
            dragdrop: true,

            // Views to activate
            views: {
                list: false,
                thumbs: true, // Show thumbs
                active: 'thumbs'
            },

            // Flash settings
            //flash_swf_url: 'http://shoko.biz/admin/views/layout/default/js/plugins/plupload/Moxie.swf',

            // Silverlight settings
            //silverlight_xap_url: 'http://shoko.biz/admin/views/layout/default/js/plugins/plupload/Moxie.xap',
            init: {
                UploadComplete: function () {
                    var uploader = $('#uploader').plupload('getUploader');
                    uploader.splice();
                    uploader.refresh();
                    load_gallery_item()

                }
            }
        });
    }

});
