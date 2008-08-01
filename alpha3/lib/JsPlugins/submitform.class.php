<?php
/**
 * Plugin TinyMce -- textový wisiwing editor
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