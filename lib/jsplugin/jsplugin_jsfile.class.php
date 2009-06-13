<?php
/**
 * Pomocná třída JsPluginu a Epluginu pro práci Js Soubory.
 * Třída slouží pro práci s javascript soubory v JsPluginu a Epluginu. Umožňuje
 * jednoduché nasatvené parametrů souboru.
 *
 * @copyright  	Copyright (c) 2009 Jakub Matas
 * @version    	$Id: jsfile.class.php 533 2009-03-29 00:11:57Z jakub $ VVE3.9.4 $Revision: 533 $
 * @author			$Author: jakub $ $Date: 2009-03-29 01:11:57 +0100 (Sun, 29 Mar 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-03-29 01:11:57 +0100 (Sun, 29 Mar 2009) $
 * @abstract 		Třida pro práci s javascript soubry
 */

class JsPlugin_JsFile {
	
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
    * jestli je soubor virtuální, neexistuje reálně na filesystému
    * @var boolean
    */
   private $virtualFile = false;

	/**
	 * Konstruktor
	 *
	 * @param string -- název souboru
	 */
	function __construct($file, $virtual = false) {
		$this->jsFile = $file;
      $this->virtualFile = $virtual;
	}
	
	/**
	 * Metoda vrací všechny parametry jako pole
	 * @return array -- pole parametrů
	 */
	public function getParams(){
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

   /**
    * Meto vrací jestli se jedná o virtuální soubor, tj. soubor, který se generuje
    * za běhu skriptu
    * @return boolean -- true pokud je virtuální
    */
   public function isVirtual() {
      return $this->virtualFile;
   }

   /**
    * Metoda nastavuje a rozparsuje parametry souboru z řetězce
    * @param string $params -- parametry v řetězci
    */
   public function setParams($params) {
		if($params != null){
			$tmpParamsArray = array();
			$tmpParamsArray = explode(Links::URL_PARAMETRES_SEPARATOR_IN_URL, $params);
			foreach ($tmpParamsArray as $fullParam) {
				$tmpParam = explode(Links::URL_SEP_PARAM_VALUE, $fullParam);
				if(isset($tmpParam[0]) AND isset($tmpParam[1])){
               $this->fileParams[rawurldecode($tmpParam[0])] = rawurldecode($tmpParam[1]);
				}
			}
		};
   }

   /**
    * Metoda nasatvuje parametr souboru
    * @param string $paramName -- název parametru
    * @param string $value  -- (option) hodnota parametru, pokud je null tak je
    * parametr smazán
    */
   public function setParam($paramName, $value = null) {
      if($value != null){
         $this->fileParams[$paramName] = $value;
      } else {
         unset ($this->fileParams[$paramName]);
      }
   }

   /**
    * Metoda vrací parametr souboru
    * @param string $param -- název parametru
    */
   public function getParam($param) {
      if(isset ($this->fileParams[$param])){
         return rawurlencode($this->fileParams[$param]);
      } else {
         return null;
      }
   }

   /**
    * Metoda vrací název souboru
    * @return string -- název souboru
    */
   public function getName() {
      return $this->jsFile;
   }
}
?>