<?php
/**
 * Třída JsPluginu PiroBOX.
 * Třída pro vkládání pluginu pro zobrazovaní popup oken s obrázkem.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: jsplugin_lightbox.class.php 650 2009-09-21 11:13:26Z jakub $ VVE3.9.1 $Revision: 650 $
 * @author			$Author: jakub $ $Date: 2009-09-21 13:13:26 +0200 (Po, 21 zář 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-09-21 13:13:26 +0200 (Po, 21 zář 2009) $
 * @abstract 		Třída JsPluginu PiroBox
 * @see           http://www.pirolab.it/pirobox/
 */

class JsPlugin_PiroBox extends JsPlugin {
   protected $config = array(
   'theme' => 'black');

	protected function initJsPlugin() {
      
	}
	
	protected function setFiles() {
      $this->addDependJsPlugin(new JsPlugin_JQuery());
//		Přidání css stylu
		$this->addFile(new JsPlugin_CssFile($this->getCfgParam('theme').'/style.css'));
		//		Přidání js soubrů pluginu
		$this->addFile(new JsPlugin_JsFile("piroBox.1_2_min.js"));
	}
}
?>