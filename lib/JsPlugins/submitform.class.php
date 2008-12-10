<?php
/**
 * Třída JsPluginu SubmitForm.
 * Třídá vkádá funkci pro tvorbu potvrzovacího dialogu.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: submitform.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída JsPluginu SubmitForm na potvrzení formuláře
 */

class SubmitForm extends JsPlugin {
	/**
	 * Metoda inicializuje JsPlugin
	 *
	 */
	protected function initJsPlugin() {
//		Název pluginu
		$this->setJsPluginName("SubmitForm");
		

	}
	
	/**
	 * Metoda inicializuje soubory JsPluginu
	 *
	 */
	protected function initFiles() {
		//		Přidání js soubrů pluginu
		$this->addJsFile(new JsPluginJsFile("submitform.js"));
	}
	
	
	/**
	 * Metda vytvoří výchozí konfigurační soubor
	 */
	public function generateFile() {
	}
}

?>