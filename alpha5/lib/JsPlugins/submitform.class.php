<?php
/**
 * Plugin SubmitForm -- potvrzování dialogů pomocí JavaScriptu
 *
 */
class SubmitForm extends JsPlugin {
	protected function initJsPlugin() {
//		Název pluginu
		$this->setJsPluginName("SubmitForm");
		
//		Přidání js soubrů pluginu
		$this->addJsFile("submitform.js");
	}
}

?>