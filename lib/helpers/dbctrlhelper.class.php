<?php
/**
 * Ttřída lokalizačního Controll Helperu
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	LocaleCtrlHelper class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: localectrlhelper.class.php 3.0.55 27.9.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro práci s lokalizačními prvky v kontroleru - helper
 */

class DbCtrlHelper extends CtrlHelper {
	/**
	 * Výchozí název sloupce s url klíčem
	 * @var string
	 */
	const DEFAULT_URL_KEY_COLUMN = 'urlkey';

	/**
	 * Minimální dálka url klíče
	 * @var integer
	 */
	const URL_KEY_MIN_LENGHT = 9;
	
	/**
	 * Maximální dálka url klíče
	 * @var integer
	 */
	const URL_KEY_MAX_LENGHT = 50;
	
	/**
	 * Proměná obsahuje konektor k databázi
	 * @var DbInterface
	 */
	private $db = null;
	
	/**
	 * Konstruktor třídy
	 *
	 */
	function __construct() {
		$this->db = AppCore::getDbConnector();
	}

	/**
	 * Metoda vytvoří unikátní url klíč v zadané tabulce
	 *
	 * @param string -- text ze kterého se má klíč generovat
	 * @param string -- název tabulky
	 * @param string -- název sloupce s urlklíči
	 * @return string -- vygenerovaný url klíč
	 */
	public function generateDatabaseUrlKey($textForGenerate, $dbTable, $urlKeyColum = self::DEFAULT_URL_KEY_COLUMN, $minLenght = self::URL_KEY_MIN_LENGHT, $maxLenght = self::URL_KEY_MAX_LENGHT) {
		$sqlSelecturlKey = $this->db->select()->from($dbTable, $urlKeyColum);
		
		$urlkeysArray = $this->db->fetchAssoc($sqlSelecturlKey);

		$newUrlKeys = array();
		foreach ($urlkeysArray as $urlKey) {
			array_push($newUrlKeys, $urlKey[$urlKeyColum]);
		}
		
		
		$urlKey = $this->createDatabaseKey($textForGenerate, $newUrlKeys, $minLenght, $maxLenght);
		
		return $urlKey;
	}
	
	
	/**
	 * Funkce vytvoří klíč o velikosti 50 znaků, který je určen pro uložení do db
	 *
	 * @param string -- text ze kterého se má klíč vytvořit
	 * @param array -- pole již existujících klíčů
	 * @param integer -- minimální počet znaků
	 * @param integer -- maximální počet znaků
	 * @return string -- vygenerovaný klíč pro db
	 */
	public function createDatabaseKey($text, $keysArray = null, $minLenght = self::URL_KEY_MIN_LENGHT, $maxLenght = self::URL_KEY_MAX_LENGHT)
	{
		//	odstranění tagů
		$text=ereg_replace("<[^>]+>", "", $text);

		//	pevod na ascii
		$textHelper = new TextCtrlHelper();
		$newKey = $textHelper->utf2ascii($text);
		unset($textHelper);
		$newKey = substr($newKey, 0, $maxLenght);

		//	Porpvnání klíču již uloženyých a vytvoření unikátního
		if($keysArray != null){
			$step = 1;
			$newUniqueKey = $newKey;
			$uniqueKey = false;

			while ($uniqueKey != true) {
				if(!in_array($newUniqueKey, $keysArray)){
					$uniqueKey = true;			
				} else {
					$newUniqueKey=$newKey."-".$step++;
				}
			}
			$newKey=$newUniqueKey;
		}

		if(strlen($newKey) < 6){
			$newKey = str_pad($newKey, $minLenght, ".", STR_PAD_BOTH);
		}
		
		return $newKey;
	}
}
?>