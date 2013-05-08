jQuery(function($) {

	// big image rotator (draft)
	function rotate() {
		var current = ($('.big-image-container img.show')? $('.big-image-container img.show') : $('.big-image-container img:first'));
		current.fadeOut("slow", function(next){
			var next = ((current.next().length) ? ((current.next().hasClass('show')) ? $('.big-image-container img:first') :current.next()) : $('.big-image-container img:first'));
			$(this).removeClass('show');
			next.addClass('show').fadeIn("slow");
		});
	}
	setInterval(rotate, 5000);

	// fancybox to edit main menu in admin mode
	$(".fancybox").fancybox();
	// page tree controls
	$("#add-node").click(function () {
        $("#cssmenu-edit").jstree("create");
    });
	$("#delete-node").click(function () {
        $("#cssmenu-edit").jstree("remove");
    });
	$("#rename-node").click(function () {
        $("#cssmenu-edit").jstree("rename");
    });
	// create page tree
	$("#cssmenu-edit").jstree({
        "ui" : {
            "initially_select" : [ "item-1" ]
        },
		"plugins" : [  "themes", "html_data", "ui", "crrm"]
    });
	
	var photo = $( "#sortable" );
	photo.sortable({
		update: function(){
			var order = photo.sortable('toArray');
			$.post('/design/sortable', {items : order}, function(data){
				/*if(data.result == 'ok'){
					$(".text-success").show();
				}*/
			});
		}
	});
	
	
});