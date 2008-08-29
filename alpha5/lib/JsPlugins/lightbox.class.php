<?php
/**
 * Plugin LightBOX -- zobrazení obrázků v popup okně
 *
 */
class LightBox extends JsPlugin {
	protected function initJsPlugin() {
//		Název pluginu
		$this->setJsPluginName("LightBox");
		
//		Výchozí js soubor pluginu
//		$this->setDefaultSettingJsFile("tiny_mce_default.js");
		
//		Přidání css stylu
		$this->addCssFile('lightbox.css');
		
//		Přidání js soubrů pluginu
		$this->addJsFile("prototype.js");
		$this->addJsFile("scriptaculous.js?load=effects");
		$this->addJsFile("lightbox.js");
		
		
		
	}
}
?>