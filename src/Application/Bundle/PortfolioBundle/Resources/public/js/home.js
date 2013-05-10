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
	// save sorting
	$("#sortable").sortable({
		update: function(){
			var order = $("#sortable").sortable('toArray');
			$.post('/design/sortable', {items : order});
		}
	});
	// create context menu
	$("#sortable").contextMenu({
		selector: 'li', 
		callback: function(key, options) {
			var img_id = $(this).attr('id').substring(6);
			$.post('/design/photo-edit', {key : key, img_id : img_id}, function(data){
				if(data.result == 'delete'){
					$('li#photo_'+data.img_id).remove();
					$('img#photo_'+data.img_id).remove();
				}
			});
        },
		items: {
            "Delete": {name: "Удалить"},
            "sep1": "---------",
			"move":{
				"name": "Переместить в...",
				"items": {
					"move-gallery-1": {"name": "галерея 1"},
					"move-gallery-2": {"name": "галерея 2"},
				}
			},
			"sep2": "---------",
			"copy":{
				"name": "Копировать в...",
				"items": {
					"copy-gallery-1": {"name": "галерея 1"},
					"copy-gallery-2": {"name": "галерея 2"},
				}
			}
        }
	});
});