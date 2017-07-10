<?php

/**
 * Třída JsPluginu SMLMenu (Simple Multi Level Menu)
 * Třída pro vkládání pluginu pro práci s menu (víceúrovňové menu, které se rouevírá při kliknutí)
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision: $
 * @author			$Author: $ $Date: $
 * 						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída JsPluginu SMLMenu
 */
class JsPlugin_BootstrapDatepicker extends JsPlugin {

   protected function initJsPlugin()
   {
      $this->setJsPluginName('bootstrap-datepicker');
      $this->setJsFilesDir('bootstrap-datepicker');
   }

   protected function setFiles()
   {
      $this->addDependJsPlugin(new JsPlugin_JQuery());
      //		Přidání js soubrů pluginu
      $this->addFile(new JsPlugin_JsFile("moment-with-locales.min.js"));
      $this->addFile(new JsPlugin_JsFile("bootstrap-datetimepicker.min.js"));
      if ($this->getCfgParam('includecss') == true) {
         $this->addFile(new JsPlugin_CssFile("bootstrap-datetimepicker.min.css"));
      }
   }

   public static function getBaseJSOptions()
   {
      $opts = array(
          'locale' => Locales::getLang(),
          'icons' => array(
              'time' => 'icon icon-clock-o',
              'date' => 'icon icon-calendar',
              'up' => 'icon icon-chevron-up',
              'down' => 'icon icon-chevron-down',
              'previous' => 'icon icon-chevron-left',
              'next' => 'icon icon-chevron-right',
              'today' => 'icon icon-screenshot',
              'clear' => 'icon icon-trash',
              'close' => 'icon icon-remove'
          )
      );
      return $opts;
   }

}
