<?php
/**
 * Pomocná třída JsPluginu a Epluginu pro práci Js Soubory.
 * Třída slouží pro práci s javascript soubory v JsPluginu a Epluginu. Umožňuje
 * jednoduché nasatvené parametrů souboru.
 *
 * @copyright  	Copyright (c) 2009 Jakub Matas
 * @version    	$Id: jsplugin_jsfile.class.php 639 2009-07-07 20:59:50Z jakub $ VVE3.9.4 $Revision: 639 $
 * @author			$Author: jakub $ $Date: 2009-07-07 22:59:50 +0200 (Út, 07 čec 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-07-07 22:59:50 +0200 (Út, 07 čec 2009) $
 * @abstract 		Třida pro práci s javascript soubry
 */

class JsPlugin_File {
	/**
	 * Název souboru
	 * @var string
	 */
	protected $file = null;

	/**
	 * Pole s parametry js souboru
	 * @var array
	 */
	protected  $fileParams = array();

   /**
    * jestli je soubor virtuální, neexistuje reálně na filesystému
    * @var boolean
    */
   protected $virtualFile = false;

   /**
    * Adresář se souborem
    * @var string
    */
   protected $dir = null;
   
   /**
    * Název pluginu
    * @var string
    */
   private $name = null;

	/**
	 * Konstruktor
	 * @param string -- název souboru
	 */
	function __construct($file, $virtual = false, $dir = null) {
		$this->file = $file;
      $this->virtualFile = $virtual;
      if($virtual){
         $this->dir = Url_Request::getBaseWebDir().Locales::getLang()."/jsplugin/JSPLUGINNAME/cat-".Category::getSelectedCategory()->getId()."/";
      } else {
         if(strncmp ($dir, 'http', 4) == 0){// celá adresa
            $this->dir = $dir;
         } else {
            $this->dir = Url_Request::getBaseWebDir(true).JsPlugin::JSPLUGINS_BASE_DIR."/JSPLUGINNAME/".$dir;
         }
      }
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
		$file = $this->getName();
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
    * @param string/array $params -- parametry v řetězci nebo pole s parametry
    */
   public function setParams($params) {
		if($params != null){
			if(is_array($params)){
            $this->fileParams = array_merge($this->fileParams, $params);
         } else {
            $tmpParamsArray = array();
      		$tmpParamsArray = explode(Url_Link::URL_PARAMETRES_SEPARATOR_IN_URL, $params);
   			foreach ($tmpParamsArray as $fullParam) {
               $tmpParam = explode(Url_Link::URL_SEP_PARAM_VALUE, $fullParam);
            	if(isset($tmpParam[0]) AND isset($tmpParam[1])){
                  $this->fileParams[rawurldecode($tmpParam[0])] = rawurldecode($tmpParam[1]);
      			}
   			}
         }
		};
   }

   /**
    * Metoda nastavuje parametr souboru
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
   public function getName($withDir = true) {
      if($withDir == true){
         return $this->getDir().$this->file;
      } else {
         return $this->file;
      }
   }
   
   /**
    * Metoda vrací název pluginu
    * @return string -- název pluginu
    */
   protected function getPluginName() {
      return $this->name;
   }

   /**
    * Metoda vrací název souboru
    * @return string -- název souboru
    */
   public function getDir() {
      return $this->dir;
   }

   /**
    * Metoda nastaví název adresáře s JSPluginem
    * @param string $name -- název pluginu
    */
   public function setPluginName($name) {
      $this->name = $name;
      $this->dir = str_replace('JSPLUGINNAME', strtolower($name), $this->dir);
   }
}