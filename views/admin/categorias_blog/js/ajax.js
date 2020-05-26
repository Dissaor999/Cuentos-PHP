
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


$(document).ready(function() {
    // multiple elements



});
