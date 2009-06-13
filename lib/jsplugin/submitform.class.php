<?php
/**
 * Třída JsPluginu SubmitForm.
 * Třídá vkádá funkci pro tvorbu potvrzovacího dialogu.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
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
	 */
	protected function initFiles() {
		//		Přidání js soubrů pluginu
		$this->addJsFile(new JsFile("submitform.js"));
	}
	
	/**
	 * Metda vytvoří výchozí konfigurační soubor
	 */
	public function generateFile(JsFile $file) {}
}
?>