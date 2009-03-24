<?php
/**
 * Abstraktní třída pro Model.
 * Třída pro základní vytvoření objektu modelu, jak souborového tak 
 * databházového. Obsahuje pouze přístup k vybranému modulu.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: model.class.php 3.0.55 26.9.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Abstraktní třída pro vytvoření modelu
 */

abstract class Model {
	/**
	 * Metoda vrací objekt modulu
	 * 
	 * @return Module -- objekt modulu
	 */
	final public function getModule() {
		return AppCore::getSelectedModule();
	}
	
	/**
	 * Abstraktní metoda pro inicializaci modelu
	 */
	protected function _init(){}	
	
}
?>