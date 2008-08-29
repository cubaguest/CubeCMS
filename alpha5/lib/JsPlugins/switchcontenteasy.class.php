<?php
/**
 * Plugin TinyMce -- textový wisiwing editor
 *
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