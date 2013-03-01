$.fn.cubeHPSlider = function (options) {

   var defaults = {
      speed: 1000,
      delay: 3000
   };

   var options = $.extend(defaults, options);

   return this.each(function () {
      $obj = $(this);

      $('.slide:first', $obj).addClass('active');
      $('.slide', $obj).not('.active').hide();

      var timer;

      // najetí myší na objekt slideru
      $obj.mouseover(function () {
         clearTimeout(timer);
      });
      $obj.mouseleave(function () {
         sliderRunAfterDelay();
      });

      // automatický cyklický přepínač tlačítek
      function sliderRun() {
         var $active = $('.slide.active', $obj);
         var $next = $active.next();
         if ($next.length == 0) {
            $next = $('.slide:first', $obj);
         }
         $active.removeClass('active').fadeOut(options.speed);
         $next.addClass('active').fadeIn(options.speed);

         sliderRunAfterDelay();
      }
      // run slider after delay
      function sliderRunAfterDelay() {
         timer = setTimeout(function () { sliderRun(); }, options.delay);
      }
      // run slider
      sliderRunAfterDelay();
   });
};