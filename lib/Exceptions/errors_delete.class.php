<?php
/**
 * Třída slouží pro obsluhu chyb ve stránce.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: errors.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro obsluhu chyb
 * @deprecated 
 * @todo odstranit (popřípadě pokud se využije dodělat popisy a dokumentaci)
 */

class Errors {
	/**
	 * Pole s chybami
	 *
	 * @var array
	 */
	private $errorsArray = array();

	/**
	 * Konstruktor třídy
	 * Vytvoří pole pro ukládání chyb
	 *
	 */
	function __construct() {
		$this->errorsArray = array();
	}

	/**
	 * Metoda přidává chybu do pole chyb
	 */
	public function addError($message) {
		array_push($this->errorsArray, $message);
	}

	/**
	 * Metody vypíše chyby na standartní výstup
	 */
	public function getErrorsToStdIO() {

		foreach ($this->errorsArray as $index => $var) {
			echo nl2br($var);
		}
	}

	/**
	 * Metoda vrací jestli je pole se zprávami prázdné
	 * @return boolean -- true pokud je pole prázdné
	 */
	public function isEmpty()
	{
		return empty($this->errorsArray);
	}
	
}


?>