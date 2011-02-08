<?php
/**
 * Třída JsPluginu LightBOX.
 * Třída pro vkládání pluginu pro zobrazovaní popup oken s obrázkem.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: jsplugin_lightbox.class.php 650 2009-09-21 11:13:26Z jakub $ VVE3.9.1 $Revision: 650 $
 * @author			$Author: jakub $ $Date: 2009-09-21 13:13:26 +0200 (Po, 21 zář 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-09-21 13:13:26 +0200 (Po, 21 zář 2009) $
 * @abstract 		Třída JsPluginu LightBox
 * @see           http://www.dynamicdrive.com/dynamicindex4/lightbox2/index.htm
 */

class JsPlugin_JCrop extends JsPlugin {
	protected function initJsPlugin() {
      $this->setJsPluginName('jqueryjcrop');
      $this->setJsFilesDir('jqueryjcrop');
	}
	
	protected function setFiles() {
      $jquery = new JsPlugin_JQuery();
      $this->addDependJsPlugin($jquery);
//		Přidání css stylu
		$this->addFile(new JsPlugin_CssFile('jquery.Jcrop.css'));
		//		Přidání js soubrů pluginu
		$this->addFile(new JsPlugin_JsFile("jquery.Jcrop.min.js"));
	}
}
?>