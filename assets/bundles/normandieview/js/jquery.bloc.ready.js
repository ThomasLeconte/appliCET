$(document).ready(function() {

	$('.deroulant > div > span').click(function() {
		
		var contenu = $(this).parent().next();
		contenu.slideToggle(200, "easeOutQuad", function(entete) {
		if (contenu.prev().hasClass("expand")) {
			contenu.prev().removeClass("expand");
			contenu.prev().addClass("collapse");
			$(this).parent().find(".icon-expand").hide();
			$(this).parent().find(".icon-collapse").show();
		} else  {
			contenu.prev().removeClass("collapse");
			contenu.prev().addClass("expand");
			$(this).parent().find(".icon-expand").show();
			$(this).parent().find(".icon-collapse").hide();
		}
		});
	});
});