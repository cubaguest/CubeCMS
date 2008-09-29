<?php
/**
 * Plugin TinyMce -- textový wisiwing editor
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	TinyMce class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: tinymce.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Třída JsPluginu TinyMce
 */

require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'JsPlugins'. DIRECTORY_SEPARATOR . 'jsplugin.calss.php');


class TinyMce extends JsPlugin {
	/**
	 * Konstanta s názvem adresáře s pluginem
	 * @var string
	 */
	const TINY_MCE_MAIN_DIR = 'tinymce';
	
	/**
	 * Název souboru s url base
	 * @var string
	 */
	const TINY_MCE_UR_BASE_FILE = 'tiny_url_base.js';
	
	protected function initJsPlugin() {
//		Název pluginu
		$this->setJsPluginName("TinyMCE");
		
//		Výchozí js soubor pluginu
		$this->setDefaultSettingJsFile("tiny_mce_default.js");
		
//		Přidání js soubrů pluginu
		$this->addJsFile("tiny_mce.js");
		

	}
	
	/**
	 * Metoda vytvoří soubor s base url, určený pro načtení do TinyMCE
	 */
	private function createBaseUrl() {
//		$files = new Files();
		
//		Vytvoření scriptu pro načtení baseurl
		$link = new Links();
		
		if(fopen(AppCore::getAppWebDir().AppCore::ENGINE_CACHE_DIR.'/'.self::TINY_MCE_UR_BASE_FILE, 'w')){
			
		}
//		<script language="javascript" type="text/javascript">
//tinyMCE.init({
//	document_base_url : "http://localhost/vve/"
//});
//</script>
	}
	
	/**
	 * Metda vytvoří výchozí konfigurační soubor
	 */
	private function createDefaultConfigFile() {
		;
	}
	
	
}

?>