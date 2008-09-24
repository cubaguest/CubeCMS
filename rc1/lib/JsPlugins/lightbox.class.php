<?php
/**
 * Plugin LightBOX -- zobrazení obrázků v popup okně
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	LightBox class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: lightbox.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Třída JsPluginu LightBox
 * @see 		http://www.dynamicdrive.com/dynamicindex4/lightbox2/index.htm
 */
class LightBox extends JsPlugin {
	protected function initJsPlugin() {
//		Název pluginu
		$this->setJsPluginName("LightBox");
		
//		Výchozí js soubor pluginu
//		$this->setDefaultSettingJsFile("tiny_mce_default.js");
		
//		Přidání css stylu
		$this->addCssFile('lightbox.css');
		
//		Přidání js soubrů pluginu
		$this->addJsFile("prototype.js");
		$this->addJsFile("scriptaculous.js?load=effects");
		$this->addJsFile("lightbox.js");
		
		
		
	}
}
?>