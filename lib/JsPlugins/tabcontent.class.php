<?php
/**
 * Plugin TabContent -- záložkové pole (např. div)
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	TabContentEasy class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: tabcontenteasy.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída JsPluginu pro záložkový box
 */

require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'JsPlugins'. DIRECTORY_SEPARATOR . 'jsplugin.calss.php');

class TabContent extends JsPlugin {
	protected function initJsPlugin() {
//		Název pluginu
		$this->setJsPluginName("TabContent");
		//		Přidání css stylu
		$this->addCssFile('tabcontent.css');
		
		
//		Přidání js soubrů pluginu
		$this->addJsFile("tabcontent.js");
		
//		$this->setDefaultSettingJsFile('default.js');
	}
}

?>