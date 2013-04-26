function rotate() {	
	var current = ($('.big-image-container img.show')? $('.big-image-container img.show') : $('.big-image-container img:first'));
	var next = ((current.next().length) ? ((current.next().hasClass('show')) ? $('.big-image-container img:first') :current.next()) : $('.big-image-container img:first'));  
	current.removeClass('show').fadeOut("slow");
	next.addClass('show').fadeIn("slow");
}
$(document).ready(function(){
	setInterval('rotate()',5000);
});