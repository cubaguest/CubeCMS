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
});