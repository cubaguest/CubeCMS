<?php
/**
 * Třída pro obsluhu konfiguračního souboru.
 * Třída slouží k získávání parametrů z konfiguračního souboru.
 * 
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro obsluhu konfiguračního souboru
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
	 * @param string -- soubor s konfigurací
	 */
	function __construct($configFile) {
		$this->_configFile = $configFile;
		$this->_configArray = array();
		$this->_initConfigFile();
	}

	/**
	 * Metoda načte konfigurační soubor do pole
	 */
	private function _initConfigFile() {
      try {
         if (!file_exists($this->_configFile)){
            throw new BadFileException(sprintf(_('Nepodařilo se otevřít konfigurační soubor "%s"'),
               $this->_configFile), 101);
         }
         $xml = simplexml_load_file($this->_configFile);
         $this->_configArray = $this->_objToArray($xml);
      } catch (BadFileException $e) {
         new CoreErrors($e);
      }
   }

	/**
	 * Metoda převede objekt SimpleXMLElement na pole
	 *
	 * @param SimpleXMLElement -- objekt elementů funkce simplexml_load_file
	 * @return array -- pole prvků
	 */
	private function _objToArray($xmlObject) {
		$return = null;
		if(is_array($xmlObject)){
			foreach($xmlObject as $key => $value){
				$return[$key] = $this->_objToArray($value);
			}
		} else {
			$var = get_object_vars($xmlObject);
			if($var){
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
      try {
         if($parentKey == null){
            if(!isset($this->_configArray[$option])){
               throw new InvalidArgumentException(sprintf(_("Volba %s není definována"), $option), 2);
            }
            return $this->_configArray[$option];
         } else {
            if(!isset($this->_configArray[$parentKey])){
               throw new InvalidArgumentException(sprintf(_("Sekce %s není definována"), $parentKey), 3);
            }

            if(!isset($this->_configArray[$parentKey][$option])){
               throw new InvalidArgumentException(sprintf(_("Volba %s v sekci %s není definována"),
                  $option, $parentKey), 4);
            }
            return $this->_configArray[$parentKey][$option];
         }
      } catch (InvalidArgumentException $e) {
         new CoreErrors($e);
      }
	}
}
?>