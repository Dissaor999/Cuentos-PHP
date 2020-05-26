$(function () {
	croppieUpload();
});
function croppieUpload() {
	var $uploadCrop;
    $('#upload').on('change', function (ev) {
        readFile(this);
    });

    $( "form" ).submit(function( event ) {
        readFile($("#upload"));
        $uploadCrop.croppie('result', {
            type: 'base64',
            format: 'jpeg',
            quality: 0.8,
            circle: false
        }).then(function (resp) {
        	var input = $("#upload");
        	if(input.val() !== ""){
                $("input[name=imgDedicatoria]").val(resp);
			}
        });
    });

	function readFile(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				$uploadCrop.croppie('bind', {
					url: e.target.result
				});
			};

			reader.readAsDataURL(input.files[0]);
		}
	}

	$uploadCrop = $('#toPrint').croppie({
		viewport: {
			width: 250,
			height: 250,
			type: 'circle'
		},
		boundary: {
			width: 250,
			height: 250
		},
		showZoomer: false,
		enableExif: true
	});
}