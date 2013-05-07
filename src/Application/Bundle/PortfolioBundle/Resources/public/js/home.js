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
	$("#cssmenu-edit").jstree({
        "plugins" : [  "themes", "html_data", "ui", "crrm", "contextmenu", "sort", "dnd"]
    });
});