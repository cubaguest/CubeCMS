<?php
/**
 * Třída pro obsluhu chyb
 * //TODO dodělat popisy a dokumentaci
 */

class Errors {
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

//		echo "<pre>";
//		print_r($this->errorsArray);
//		echo "</pre>";
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