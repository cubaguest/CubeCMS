<?php
/**
 * Pomocná třída JsPluginu.
 * Třída slouží pro práci s javascript soubory v JsPluginu. Umožňuje 
 * jednoduché nasatvené parametrů souboru.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: jspluginjsfile.class.php 3.1.8 beta1 13.11.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třida pro práci s javascript soubry JsPluginu
 */

class JsPluginJsFile {
	
	/**
	 * Název js souboru 
	 *
	 * @var string
	 */
	private $jsFile = null;
	
	/**
	 * Pole s parametry js souboru
	 * @var array
	 */
	private $fileParams = array();
	
	/**
	 * Konstruktor
	 *
	 * @param string -- název souboru
	 */
	function __construct($file) {
		$this->jsFile = $file;
	}
	
	/**
	 * Metoda nastaví parametr souboru
	 *
	 * @param string -- název parametru
	 * @param mixed -- hodnota parametru
	 */
	public function setParam($paramName, $paramValue) {
		$this->fileParams[$paramName] = $paramValue;
	}
	
	/**
	 * Metoda vrací všechny parametry jako pole
	 * @return array -- pole parametrů
	 */
	public function getParams() {
		return $this->fileParams;
	}
	
	/**
	 * Metoda převede soubor na řetězec a přidá za něj parametry
	 *
	 * @return string -- soubor s parametry
	 */
	function __toString() {
		$file = $this->jsFile;
		
		if(!empty($this->fileParams)){
			$params = http_build_query($this->fileParams);
			$file.='?'.$params;
		}
		
		return $file;
	}
	
}

?>