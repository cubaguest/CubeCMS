<?php
/**
 * Plugin SwitchContent -- rozbalovací pole (např. div)
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	SwitchContentEasy class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: switchcontenteasy.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Třída JsPluginu pro rozbalovací box
 */
class SwitchContentEasy extends JsPlugin {
	protected function initJsPlugin() {
//		Název pluginu
		$this->setJsPluginName("SwitchContentEasy");
		
//		Přidání js soubrů pluginu
		$this->addJsFile("switchcontent.js");
	}
}

?>