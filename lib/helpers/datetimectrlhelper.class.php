<?php
/**
 * Ttřída Controll Helperu pro práci s datumy a časy
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	DateTimeCtrlHelper class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: datetimectrlhelper.class.php 3.0.55 27.9.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Třída pro práci s datumi a časi v kontroleru - helper
 */

class DateTimeCtrlHelper extends CtrlHelper {
	/**
	 * KOnstanta s názvem pole z $_POST s dny
	 * @var string
	 */
	const POST_DATE_ARRAY_DAY_KEY = 'Date_Day';
	const POST_DATE_ARRAY_MONTH_KEY = 'Date_Month';
	const POST_DATE_ARRAY_YEAR_KEY = 'Date_Year';

	/**
	 * Konstruktor třídy
	 *
	 */
	function __construct() {
	}
	
	/**
	 * Metoda vrací časové razítko ze zadaného pole, vygenerovaného pomocí SMARTY
	 *
	 * @param string -- název $_POST pole s prvky datumu
	 */
	public function createStampSmartyPost($postName) {
		$day = $month = $year = $hour = $minute = $second = null;
		
		if(isset($_POST[$postName][self::POST_DATE_ARRAY_DAY_KEY])){
			$day = (int)$_POST[$postName][self::POST_DATE_ARRAY_DAY_KEY];
		}
		
		if(isset($_POST[$postName][self::POST_DATE_ARRAY_MONTH_KEY])){
			$month = (int)$_POST[$postName][self::POST_DATE_ARRAY_MONTH_KEY];
		}
		
		if(isset($_POST[$postName][self::POST_DATE_ARRAY_YEAR_KEY])){
			$year = (int)$_POST[$postName][self::POST_DATE_ARRAY_YEAR_KEY];
		}
		
		//TODO dodělat ještě hodiny:minuty:sekundy
		
		$timestamp = mktime($hour, $minute, $second, $month, $day, $year);
		
		return $timestamp;
	}
	
	/**
	 * Metoda vrací dané období (jaro, léto , podzim, zima)
	 *
	 * @param integer -- timestamp
	 * @return integer -- číslo období (0-3)
	 */
	public function getSeason($timestamp){
		$month = date("n",$timestamp);
  		$day = date("j",$timestamp);
  
  		switch($month){
    		case 1: case 2: return 3;
    		case 3: if ($day < 21) return 3; else return 0;
    		case 4: case 5: return 0;
    		case 6: if ($day < 21) return 0; else return 1;  
    		case 7: case 8: return 1;
    		case 9: if ($day < 23) return 1; else return 2;
    		case 10: case 11: return 2;
    		case 12: if ($day < 21) return 2; else return 3;  
  		}
	}
	
}
?>