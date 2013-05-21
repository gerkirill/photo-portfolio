$(function() {
	$(".add-photos").click(function () {
        $("#uploader").show();
		var id = $(this).attr('id');
		$("#uploader").pluploadQueue({
			// General settings
			runtimes : 'html5,browserplus,flash,gears,silverlight,html4',
			url : '/design/upload/'+id,
			max_file_size : '10mb',
			chunk_size : '1mb',
			rename : true,
			filters : [
				{title : "Image files", extensions : "jpg,gif,png"},
				{title : "Zip files", extensions : "zip"}
			],

			// Resize images on clientside if we can
			//resize : {width : 320, height : 240, quality : 90},

			// Flash settings
			 flash_swf_url : '/plupload.flash.swf',
			
			// Silverlight settings
			 silverlight_xap_url : '/plupload.silverlight.xap'
		});
    });
});