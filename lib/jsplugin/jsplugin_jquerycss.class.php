<?php
/**
 * Třída JsPluginu JQuery CSS soubory
 *
 * @copyright  	Copyright (c) 2009 Jakub Matas
 * @version    	$Id: jsplugin_jquery.class.php 1278 2010-05-19 23:35:22Z jakub $ VVE3.3.0 $Revision: 1278 $
 * @author        $Author: jakub $ $Date: 2010-05-20 01:35:22 +0200 (Thu, 20 May 2010) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2010-05-20 01:35:22 +0200 (Thu, 20 May 2010) $
 * @abstract 		Třída JsPluginu JQuery css styly
 * @link          http://jquery.com/
 */

class JsPlugin_JQueryCSS extends JsPlugin_JQuery {
//    /**
//     * Pole s konfigurací pluginu
//     * @var array
//     */
//    protected $config = array('theme' => JsPlugin_JQuery::BASE_THEME);
//
// 	protected function initJsPlugin() {
//       $this->setJsFilesDir('jquery');
//       if(defined('VVE_JQUERY_THEME')){
//          $this->setCfgParam('theme', VVE_JQUERY_THEME);
//       }
//    }

	protected function setFiles() {
      $this->addFile(new JsPlugin_CssFile("jquery.ui.core.css",false,  JsPlugin_JQuery::getThemeDir($this->getCfgParam('theme'))));
      $this->addFile(new JsPlugin_CssFile("jquery.ui.theme.css",false, JsPlugin_JQuery::getThemeDir($this->getCfgParam('theme'))));
	}
   
   protected function addJs($name) {
   }
}
