// JavaScript Document

$(function () {    
  /*$('.menu li').hover(function () {
     clearTimeout($.data(this, 'timer'));
     $('ul', this).stop(true, true).slideDown();
  }, function () {
    $.data(this, 'timer', setTimeout($.proxy(function() {
      $('ul', this).stop(true, true).slideUp(500);
    }, this),-0));
  });*/
});
  $(document).ready(function(){
  $(".glyphicon-search").click(function(e) {
       $("#search").toggle(); 
    });
  })
$(window).scroll(function(){
   var sticky = $('.sticky'),
    scroll = $(window).scrollTop();
 
   if (scroll >= 10) sticky.addClass('fixed');
   else sticky.removeClass('fixed');
 });
 $(document).ready(function(e){
    
    $(".submenu").click(function(){
        $(".down1").show();
    });
});


