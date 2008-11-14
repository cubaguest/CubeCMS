<?php
/**
 * Pomocná třída Jspluginu pro práci s javascript soubory
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	TinyMce class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: jspluginjsfile.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třida zjednodušuje práci s javascript soubry JsPluginu
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