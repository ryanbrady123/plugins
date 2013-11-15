/* dCodes Framework:(c) TemplateAccess */

jQuery(function($) {
	$(".dc_content_tooltip").hover(function(){
		tip = $(this).find('.dc_content_tooltip_container');
		tip.show();
	}, function() {
		tip.hide();
	}).mousemove(function(e) {
		var mousex = e.pageX + 20;
		var mousey = e.pageY + 20;
		var tipWidth = tip.width();
		var tipHeight = tip.height();
		var tipVisX,tipVisY;
		
			tipVisX= $(window).width() - (mousex + tipWidth)-$(".main").css('margin-left');
			tipVisY  = $(window).height() - (mousey + tipHeight)-$(".main").css('margin-top');

		if ( tipVisX < 20 ) {
			mousex = e.pageX - tipWidth - 20;
		} if ( tipVisY < 20 ) {
			mousey = e.pageY - tipHeight - 20;
		} 
		if($(this).parents(".metro_menu").length){
			mousex=mousex -$(this).parents("li").parents("li").position().left;
			mousey  = mousey-$(this).parents("li").parents("li").position().top-20;
		}
		tip.css({  top: mousey, left: mousex });
	});
});

