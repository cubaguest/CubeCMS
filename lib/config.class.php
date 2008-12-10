<?php
/**
 * Třída pro obsluhu konfiguračního souboru.
 * Třída slouží k získávání parametrů z konfiguračního souboru.
 * 
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id:config.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro obsluhu konfiguračního souboru
 * 
 */

class Config {
	/**
	 * Název sekce s tabulkami v databázi
	 * @var string
	 */
	const SECTION_DB_TABLES = 'db_tables';
	
	/**
	 * Název konfiguračního souboru
	 * @var string
	 */
	private $_configFile = null;

	/**
	 * Pole objektů s konfiguračními volbami
	 * @var ArrayObject
	 */
	private $_configArray = null;

	/**
	 * Konstruktor třídy
	 *
	 * @param string -- soubor s konfigurací
	 */
	function __construct($configFile, &$coreErrors) {
		$this->_configFile = $configFile;
		$this->_configArray = array();
		$this->_initConfigFile();
	}

	/**
	 * Metoda načte konfigurační soubor do pole
	 */
	private function _initConfigFile() {
		if (file_exists($this->_configFile)) {
   			$xml = simplexml_load_file($this->_configFile);

   			$this->_configArray = $this->_objToArray($xml);
		} else {
			throw new CoreException(_('Nepodařilo se otevřít konfigurační soubor ') . $this->_configFile, 101);
		};
	}

	/**
	 * Metoda převede objekt SimpleXMLElement na pole
	 *
	 * @param SimpleXMLElement -- objekt elementů funkce simplexml_load_file
	 * @return array -- pole prvků
	 */
	private function _objToArray($xmlObject) {
		$return = null;

		if(is_array($xmlObject))
		{
			foreach($xmlObject as $key => $value){
				$return[$key] = $this->_objToArray($value);
			}
		}
		else
		{
			$var = get_object_vars($xmlObject);

			if($var)
			{
				foreach($var as $key => $value){
					$return[$key] = $this->_objToArray($value);
				}
			} else {
				return $xmlObject;
			}
		}
		return $return;
	}

	/**
	 * Vrátí požedovanou hodnotu z konfiguračního souboru
	 *
	 * @param string -- volba, která se má vrátit
	 * @param string -- skupina volby, která se má vrátit
	 * @todo dodělat vracení z větší hloubky stromu
	 */
	public function getOptionValue($option, $parentKey = null){
		$return = null;
		if($parentKey == null){
				if(!isset($this->_configArray[$option])){
					$error = new CoreException(_("Volba ").$option._(" není definována"), 102);
				} else {
					$return = $this->_configArray[$option];
				}
		} else {
				if(!isset($this->_configArray[$parentKey][$option])){
					$error = new CoreException(_("Volba ").$option._(" v sekci ").$parentKey._("není definována"), 103);
				} else {
					return $this->_configArray[$parentKey][$option];
				}
		}
		return $return;
	}

}



?>