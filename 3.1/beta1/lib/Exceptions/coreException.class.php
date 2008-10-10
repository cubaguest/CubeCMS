<?php
/**
 * Třída pro obsluhu chybových hlášek
 * Rozšiřuje třídu Exception
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	CoreExceptions class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: coreexception.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Třída pro zachtáváni chyb enginu
 * //TODO not full implemented
 */

class CoreException extends Exception {

	static private $errors = array();

	/**
	 * Konstruktor
	 *
	 */
	function __construct($message, $code = 0) {
		parent::__construct($message, $code);

		self::addError($this);
	}

	public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message} in file {$this->getFile()} on line {$this->getLine()}\n";
    }

    /**
     * Metoda vygeneruje chbovou hlášku
     *
     * @return string -- vygenerovaná hláška
     */
    public function getException() {
    	$trace = $this->getTrace();
    	return __CLASS__ . ": [{$this->code}]: {$this->message} in file {$this->getFile()} on line {$this->getLine()}\n trace: file: [{$trace[0]["file"]}]: on line {$trace[0]["line"]}\n";
    }

    /**
     * Statická funkce nastaví zachytávač chyb
     *
     * @param Errors -- error handler
     */
    static function _setErrorsHandler(&$errorHandler) {
    	self::$errors = $errorHandler;
    }

    /**
     * Statická metoda přidá vyjímku do pole chyb
     * @package Exception -- vijímka
     */
    public static function addError(Exception $exception) {
    	if(!is_array(self::$errors)){
    		self::$errors = array();
    	}

    	$errorArray = array();
    	$errorArray["name"] = $exception->getMessage();
    	$errorArray["code"] = $exception->getCode();
    	$errorArray["line"] = $exception->getLine();
    	$errorArray["file"] = $exception->getFile();

    	array_push(self::$errors, $errorArray);
    }

	public static function getAllExceptions(){
//		echo "<pre>";
//		print_r(self::$errors);
//		echo "</pre>";
		return self::$errors;
	}

	/**
	 * Metoda vrací jestli je pole se zprávami prázdné
	 * @return boolean -- true pokud je pole prázdné
	 */
	public static function isEmpty(){
//		echo "<pre>";
//		print_r(self::$errors);
//		echo "</pre>";
//		echo "empty: ".self::$errors->isEmpty();
//		return self::$errors->isEmpty();
//		if(is_array(self::$errors)){
//			return false;
//		} else {
//			return true;
//		}
		return !is_array(self::$errors);
	}

}

?>