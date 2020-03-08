function JQButtonReady()
{
	$('.JQButton').wrap('<span class="JQButton_wrapper" />');
	
	$("button, .JQButton").button();
	
	
}

$(document).ready(function(){
	JQButtonReady();
	
});