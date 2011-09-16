/*
 * SMLMenu 0.0.3
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
         this.$settings = $.extend({}, settings);
         
         var $ulroot = $(this).addClass(this.$settings.rootClass).children('ul').addClass(this.$settings.ulClass);
         $ulroot.find('ul').hide(); // hide all menus except top
         
         var $a = $('a', $ulroot);
         if(this.$settings.image != null){
            var $img = $('<img />').attr({src: this.$settings.image, alt : ''});
            $a.append($img);
         }
         $a.each(function(){
            var l = $(this).next('ul');
            if($(this).next('ul').length != 0){
               $(this).addClass('expandable')
               if(getSettings(this).event != null){
                  $(this).bind(getSettings(this).event, showSubMenu);
               }
            }
         });
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
      $parent.find('a').removeClass(settings.activeClass);
      $(this).addClass(settings.activeClass);
      var h = $ul.height();
      $(this).parents('ul').each(function(){
         h += $(this).height();
      });
      $('div.smlmenu_root').animate({height: h}, settings.showDelay);
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
    * @todo tady přidat zobrazení prvniho potomka
    */
   function showVisible($ulRoot){
      var $settings = getSettings($ulRoot);
      var $a = $($ulRoot).find('a.'+$settings.selectedClass);
      
      // zobrazení nadřazených položek a výpočet výšky pro ně
      var $parents = $a.parents('ul');
      $parents.reverse();
      $parents.each(function(){
         if(!$(this).hasClass($settings.ulClass)) {
            $('div.smlmenu_root').height($('div.smlmenu_root').height()+$(this).height());
            $(this).show();
         }
      });
      // zobrazení potomka pokud je
      var $child = $a.next('ul');
      if($child.length != 0){
         $('div.smlmenu_root').height($('div.smlmenu_root').height()+$child.height());
         $child.show();
      }
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
      rootClass : 'smlmenu_root',
      ulClass : 'smlmenu_ul_root',
      selectedClass : 'smlmenu_selected',
      activeClass : 'smlmenu_active',
      openClass : 'smlmenu_open',
      hoverClass : 'smlmenu_hover',
      image : null
   };

   /**
    * Metoda pro nastavení pozice submenu
    */
   $.fn.setSubMenuPosition = function(){
      return $(this).css({
//            position : 'absolute',
//            top : $(this).parents('ul').height(),
//            left : 0
      });
   };
   /**
    * metoda pro otočení pole
    */
   $.fn.reverse = [].reverse;
})(jQuery);