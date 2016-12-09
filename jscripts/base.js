$(document).ready(function(){
   $('.expand-content').each(function(){
      var $cnt = $('.content', this).hide();
      $(this).on('click', '.header a', function(){
         if($cnt.is(':visible')){
            $cnt.slideUp();
         } else {
            $cnt.slideDown();
         }
         return false;
      });
   });
    $('.block-aut-height').each(function () {
        var maxHeight = 0;
        $(this).find('.aut-height').each(function () {
            if ($(this).outerHeight() > maxHeight) {
                maxHeight = $(this).outerHeight();
            }
        });
        $(this).find('.aut-height').height(maxHeight);
    });
   
});