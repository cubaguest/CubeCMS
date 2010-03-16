/*
 * SMLMenu 0.0.2
 * http://www.vveframework.eu/jquery/smlmenu
 *
 * Copyright (c) 2010 Jakub Matas (vypecky.info)
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Date: 2010-03-03
 *
 */
(function($){
   /**
    * Konstruktor pluginu
    */
   $.fn.smlMenu = function(inSettings) {
      var settings = $.extend({}, arguments.callee.defaults, inSettings);
      return this.each(function() {
         var $ulroot = $(this).children('ul');
         this.$settings = $.extend({}, settings);
         // position relative of container
         $(this).css('position', 'relative').addClass('smlmenu_root');
         $ulroot.addClass('smlmenu_ulroot').find('ul').hide();
         $(this).height($ulroot.height());
         var $as = $('a[href=#]', $ulroot);
         if(this.$settings.image != null){
            var $img = $('<img />').attr({src: this.$settings.image, alt : ''});
            $as.append($img);
         }
         $as.bind(this.$settings.event, showSubMenu);
         $('a', $ulroot).bind('mouseover', mouseOverMenuItem);
         $('a', $ulroot).bind('mouseout', mouseOutMenuItem);
         showVisible($ulroot);
      });
   };

   /**
    * metoda zobrzí položku podmenu
    */
   function showSubMenu(){
      // Get our settings object
		var settings = getSettings(this);
      var $ul = $(this).next('ul');
      var $parent = $ul.parent('li').parent('ul');
      $parent.find('ul').hide();
      $parent.find('a').removeClass(settings.selClass);
      $(this).addClass(settings.selClass);
      var numParents = $(this).parents('ul').length+1;
      $('div.smlmenu_root').animate({height: numParents*$ul.height()}, settings.showDelay);
      $ul.setSubMenuPosition().show();
//      $ul.setSubMenuPosition().slideDown(settings.showDelay);
      return false;
   }

   function mouseOverMenuItem(){
      var settings = getSettings(this);
      $(this).parents('div.smlmenu_root').find('a').removeClass(settings.hoverClass);
      $(this).addClass(settings.hoverClass);
   }

   function mouseOutMenuItem(){
      var settings = getSettings(this);
      $(this).parents('div.smlmenu_root').find('a').removeClass(settings.hoverClass);
   }

   /**
    * funkce zobrazí vybrané menu
    */
   function showVisible(menuBox){
      var settings = getSettings(menuBox);
      var $a = $(menuBox).find('a.'+settings.visibleClass);
      $a.addClass(settings.actualClass);
      var $parents = $a.parents('ul');
      $parents.reverse();
      $parents.each(function(){
         $(this).prev('a').addClass(settings.actualClass);
         if(!$(this).hasClass('smlmenu_ulroot')) {
         $('div.smlmenu_root').height($('div.smlmenu_root').height()+$(this).height());
            $(this).setSubMenuPosition().show();
         }
      });
   }

   /**
    * Funkce vrací nasatvení menu
    */
   function getSettings(el) {
      return $(el).parents('div.smlmenu_root')[0].$settings;
	}

   // Výchozí nasatvení pluginu
   $.fn.smlMenu.defaults = {
      showDelay: 300,
      hideDelay: 0,
      event : 'click',
      selClass : 'smlmenu_selected',
      actualClass : 'smlmenu_actual',
      visibleClass : 'smlmenu_visible',
      hoverClass : 'smlmenu_hover',
      image : 'open.png'
   };

   /**
    * Metoda pro nastavení pozice submenu
    */
   $.fn.setSubMenuPosition = function(){
      return $(this).css({
            position : 'absolute',
            top : $(this).parents('ul').height(),
            left : 0
      });
   };
   /**
    * metoda pro otočení pole
    */
   $.fn.reverse = [].reverse;
})(jQuery);