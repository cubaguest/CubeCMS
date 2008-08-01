<?php
/**
 * Plugin TinyMce -- textový wisiwing editor
 *
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