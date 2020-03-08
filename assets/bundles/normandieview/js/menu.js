(function($) {
$.fn.menumaker = function(options) {  
 var cssmenu = $(this), settings = $.extend({
   format: "dropdown",
   sticky: false
 }, options);
 return this.each(function() {
   $(this).find(".button").on('click', function(){
     $(this).toggleClass('menu-opened');
     var mainmenu = $(this).next('ul');
     if (mainmenu.hasClass('menu_open')) { 
       mainmenu.slideToggle(400, function(){$(this).addClass('menu_close').removeClass('menu_open').removeAttr('style');});
     }
     else {
   	        mainmenu.slideToggle(400, function(){$(this).removeClass('menu_close').addClass('menu_open').removeAttr('style');});
   	        if (settings.format === "dropdown") 
   	        	{
   	        	mainmenu.find('ul').show().removeAttr('style');
   	        	mainmenu.find('ul').removeAttr('style');
   	        	}
     	}
   });
   cssmenu.find('li ul').parent().addClass('has-sub');
multiTg = function() {
     cssmenu.find(".has-sub").prepend('<span class="submenu-button"></span>');
     cssmenu.find('.submenu-button').on('click', function() {
       $(this).toggleClass('submenu-opened');
       if ($(this).siblings('ul').hasClass('menu_open')) {
         $(this).siblings('ul').removeClass('menu_open').slideToggle();
         $(this).siblings('ul').addClass('menu_close');
       }
       else {
         $(this).siblings('ul').addClass('menu_open').slideToggle();
         $(this).siblings('ul').removeClass('menu_close');
       }
            });
     cssmenu.find('.has-sub').children('a').on('click', function() {
    	 if ($(this).parent().parent().hasClass('menu_open'))
    		 {
         $(this).siblings('span').toggleClass('submenu-opened');
         if ($(this).siblings('ul').hasClass('menu_open')) {
        	 $(this).siblings('ul').removeClass('menu_open').slideToggle();
             $(this).siblings('ul').addClass('menu_close');
         }
         else {
        	 $(this).siblings('ul').addClass('menu_open').slideToggle();
             $(this).siblings('ul').removeClass('menu_close');
         }
    		 }
       });
   };
   if (settings.format === 'multitoggle') multiTg();
   else cssmenu.addClass('dropdown');
   if (settings.sticky === true) cssmenu.css('position', 'fixed');
 });
  };
})(jQuery);

(function($){
$(document).ready(function(){
$(".menu_liste").menumaker({
   format: "multitoggle"
});
});
})(jQuery);
