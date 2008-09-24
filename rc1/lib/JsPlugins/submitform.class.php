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
		
//		Přidání js soubrů pluginu
		$this->addJsFile("submitform.js");
	}
}

?>