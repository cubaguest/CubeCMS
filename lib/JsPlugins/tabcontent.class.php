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
class TabContent extends JsPlugin {
	protected function initJsPlugin() {
//		Název pluginu
		$this->setJsPluginName("TabContent");
	}
	protected function initFiles() {
		$this->addCssFile('tabcontent.css');
		
//		Přidání js soubrů pluginu
		$this->addJsFile(new JsPluginJsFile("tabcontent.js"));
	}
	
	
	/**
	 * Metda vytvoří výchozí konfigurační soubor
	 */
	protected function generateFile() {
		
	}
}

?>