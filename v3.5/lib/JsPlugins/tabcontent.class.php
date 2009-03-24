<?php
/**
 * Třída JsPluginu TabContent.
 * Třída slouží pro práci se záložkovým menu (tj. boxy se záložkovým 
 * přepínáním obsahu). Je úzce zpata z šablonou.
 * //TODO dodělat tvorbu scriptu pro spuštění, tak aby se dal vložit přímo do šablony a ghenerován byl zde.
 *
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