<?php
/**
 * Abstraktní třída pro Model.
 * Třída pro základní vytvoření objektu modelu, jak souborového tak 
 * databházového. Obsahuje pouze přístup k vybranému modulu.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
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