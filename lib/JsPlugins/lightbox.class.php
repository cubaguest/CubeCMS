<?php
/**
 * Třída JsPluginu LightBOX.
 * Třída pro vkládání pluginu pro zobrazovaní popup oken s obrázkem.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: lightbox.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída JsPluginu LightBox
 * @see 			http://www.dynamicdrive.com/dynamicindex4/lightbox2/index.htm
 */

class LightBox extends JsPlugin {
	protected function initJsPlugin() {
//		Název pluginu
		$this->setJsPluginName("LightBox");
		
//		Výchozí js soubor pluginu
//		$this->setDefaultSettingJsFile("tiny_mce_default.js");
		

		
//		Přidání js soubrů pluginu
//		$this->addJsFile("prototype.js");
//		$this->addJsFile("scriptaculous.js?load=effects");
//		$this->addJsFile("lightbox.js");
		
		
		
	}
	
	protected function initFiles() {
//      $this->addDependJsPlugin(new JQuery()); // závislost na jquery
		//		Přidání css stylu
		$this->addCssFile('jquery.lightbox-0.5.css');
				
		//		Přidání js soubrů pluginu
		$this->addJsFile(new JsPluginJsFile("jquery.lightbox-0.5.pack.js"));
//		$this->addJsFile(new JsPluginJsFile("jquery.lightbox-0.5.js"));

  
		//		Výchozí js soubor pluginu
//		$this->setDefaultSettingJsFile(new JsPluginJsFile("tiny_mce_default.js"));
		
//		Přidání js soubrů pluginu
//		$mainFile = new JsPluginJsFile("tiny_mce.js");
//		$mainFile->setParam('pokus', 'test');
//		$this->addJsFile($mainFile);;
	}
	
	
	/**
	 * Metda vytvoří výchozí konfigurační soubor
	 */
	public function generateFile() {
//		echo $_SERVER["QUERY_STRING"].'<br>';
//		echo $_GET['theme'].'  '.$_GET['file'];
	}
}
?>