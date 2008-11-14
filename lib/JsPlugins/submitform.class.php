<?php
/**
 * Plugin SubmitForm -- potvrzování dialogů pomocí JavaScriptu
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	SubmitForm class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: submitform.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Třída JsPluginu SubmitForm na potvrzení formuláře
 */
class SubmitForm extends JsPlugin {
	protected function initJsPlugin() {
//		Název pluginu
		$this->setJsPluginName("SubmitForm");
		

	}
	
	protected function initFiles() {
		//		Přidání js soubrů pluginu
		$this->addJsFile(new JsPluginJsFile("submitform.js"));
		//		Výchozí js soubor pluginu
//		$this->setDefaultSettingJsFile(new JsPluginJsFile("tiny_mce_default.js"));
		
//		Přidání js soubrů pluginu
//		$mainFile = new JsPluginJsFile("tiny_mce.js");
//		$mainFile->setParam('pokus', 'test');
//		$this->addJsFile($mainFile);;
	}
	
	
	/**
	 * Metda vytvoří výchozí konfigurační soubor
	 */
	public function generateFile() {
//		echo $_SERVER["QUERY_STRING"].'<br>';
//		echo $_GET['theme'].'  '.$_GET['file'];
	}
}

?>