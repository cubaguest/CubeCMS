<?php
/**
 * Třída JsPluginu LightBOX.
 * Třída pro vkládání pluginu pro zobrazovaní popup oken s obrázkem.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.1 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída JsPluginu LightBox
 * @see           http://www.dynamicdrive.com/dynamicindex4/lightbox2/index.htm
 */

class JsPlugin_LightBox extends JsPlugin {
	protected function initJsPlugin() {
//		Název pluginu
		$this->setJsPluginName("LightBox");
	}
	
	protected function initFiles() {
      $this->addDependJsPlugin(new JsPlugin_JQuery());
//		Přidání css stylu
		$this->addCssFile('jquery.lightbox-0.5.css');
		//		Přidání js soubrů pluginu
		$this->addJsFile(new JsPlugin_JsFile("jquery.lightbox-0.5.pack.js"));
	}
	
	/**
	 * Metda vytvoří výchozí konfigurační soubor
	 */
	public function generateFile(JsPlugin_JsFile $file) {}
}
?>