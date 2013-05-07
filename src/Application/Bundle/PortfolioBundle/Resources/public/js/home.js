function rotate() {	
	var current = ($('.big-image-container img.show')? $('.big-image-container img.show') : $('.big-image-container img:first'));
	current.fadeOut("slow", function(next){
		var next = ((current.next().length) ? ((current.next().hasClass('show')) ? $('.big-image-container img:first') :current.next()) : $('.big-image-container img:first'));  
		$(this).removeClass('show');
		next.addClass('show').fadeIn("slow");
	});
}
$(document).ready(function(){
	setInterval('rotate()',5000);
	$(".fancybox").fancybox();
	$("#add-node").click(function () {
        $("#cssmenu-edit").jstree("create");
    });
	$("#delete-node").click(function () {
        $("#cssmenu-edit").jstree("remove");
    });
	$("#rename-node").click(function () {
        $("#cssmenu-edit").jstree("rename");
    });

	$("#cssmenu-edit").jstree({
        "ui" : {
            "initially_select" : [ "item-1" ]
        },
		"plugins" : [  "themes", "html_data", "ui", "crrm"]
    });
});