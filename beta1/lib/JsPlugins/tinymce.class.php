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
class TinyMce extends JsPlugin {
	protected function initJsPlugin() {
//		Název pluginu
		$this->setJsPluginName("TinyMCE");
		
//		Výchozí js soubor pluginu
		$this->setDefaultSettingJsFile("tiny_mce_default.js");
		
//		Přidání js soubrů pluginu
		$this->addJsFile("tiny_mce.js");
	}
}

?>