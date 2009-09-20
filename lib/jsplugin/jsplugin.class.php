<?php
/**
 * Abstraktní třída pro obsluh JavaScript Pluginů JsPlugins.
 * Třída slouží jako základ pro tvorbu JsPluginů a jejich implementaci. Poskytuje 
 * základní přístup k parametrům JsPlugin. Umožňuje také přímé generování souborů
 * pro tvorbu dynamických nastavení a obsahů. Vše je ovládáno přes poheld (viewer).
 * 
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Abstraktní třída pro práci s JsPluginy
 */

abstract class JsPlugin {
	/**
	 * Konstanta s název adresáře JsPluginů
	 * @var string
	 */
	const JSPLUGINS_BASE_DIR = 'jscripts';
	
   /**
    * Název virtuálního adresáře s pluginem
    */
   const VIRTUAL_DIR_PREFIX = 'jsplugin';

	/**
	 * Pole obsahuje seznam Js souborů nutných pro načtení
	 * @var array
	 */
	private $jsFilesArray = array();
	
	/**
	 * Pole obsahuje seznam css souborů nutných pro načtení
	 * @var array
	 */
	private $cssFilesArray = array();
	
	/**
	 * Název JsPluginu -- je totožný s adresářem js pluginu
	 * @var string
	 */
	protected $jsPluginName = null;
	
	/**
	 * Pole s parametry JsPluginu
	 * @var array
	 */
	private $pluginParams = array();
	
   /**
    * Pole s konfigurací pluginu
    * @var array
    */
   protected $config = array();

	/**
	 * Konstruktor třídy
	 */
	final public function __construct(){
      $this->jsPluginName = str_ireplace(__CLASS__.'_', '', get_class($this));
      $this->initJsPlugin();
	}
	
	/**
	 * Třída, která se provede při inicializaci pluginu
	 */
	protected function initJsPlugin(){}
	
	/**
	 * Metoda inisializuje všechny soubory, se kterými JsPlugin pracuje
	 */
	protected abstract function setFiles();
	
	/**
	 * Metoda pro spuštění akce JsPluginu
	 */
	public function runAction($actionName, $params, $outputType){
      $this->pluginParams = $params;
      if(method_exists($this, $actionName.ucfirst($outputType).'View')){
         $this->{$actionName.ucfirst($outputType).'View'}();
      } else if(method_exists($this, $actionName.'View')) {
         $this->{$actionName.'View'}();
      } else {
         trigger_error(_('Neimplementována metoda JsPluginu'));
      }
   }

	/**
	 * Metoda přídává js soubor se scriptem
	 * @param JsPlugin_JsFile -- název js souboru
	 */
	final protected function addJsFile(JsPlugin_JsFile $jsFile){
      if(!in_array($jsFile->getName(), $this->jsFilesArray)){
         array_push($this->jsFilesArray, $jsFile);
      }
	}
	
	/**
	 * Metoda přídává css soubor js pluginu
	 * @param string -- název css souboru
	 */
	final protected function addCssFile($cssFile){
		array_push($this->cssFilesArray, $cssFile);
	}
	
   /**
    * Metoda přidá JsPlugin do závislosti
    * @param JsPlugin $jsplugin -- objekt JsPluginu 
    */
   final protected function addDependJsPlugin(JsPlugin $jsplugin) {
      $file = $jsplugin->getAllJsFiles();
      foreach ($file as $f) {
         $this->addJsFile(new JsPlugin_JsFile($f));
      }
      $file = $jsplugin->getAllCssFiles();
      foreach ($file as $f) {
         $this->addCssFile($f);
      }
   }

	/**
	 * Metoda vrací pole všech Js souborů pluginu i s cestami
    * @return array -- pole js souborů
	 */
	final public function getAllJsFiles(){
      if(empty ($this->jsFilesArray)){
         $this->setFiles();
      }
      $filesArray = array();
      //			Vložení js souborů pluginu
      if(!empty($this->jsFilesArray)) {
         foreach ($this->jsFilesArray as $jsFile) {
            array_push($filesArray, str_replace('JSPLUGINNAME', strtolower($this->jsPluginName), $jsFile->getName()));
         }
      }
      return $filesArray;
	}
	
	/**
	 * Metoda vrací pole všech Css souborů pluginu
	 *@return array -- pole Css souborů
	 */
	final public function getAllCssFiles(){
      if(empty ($this->cssFilesArray)){
         $this->setFiles();
      }
      $filesArray = array();
		//			Vložení css souborů pluginu
      if(!empty($this->cssFilesArray)) {
         $cssFiles = $this->cssFilesArray;
         foreach ($cssFiles as $cssFile) {
            array_push($filesArray, $this->getDir().$cssFile);
         }
      }
      return $filesArray;
	}
	
	/**
	 * Metoda vrací název adresáře pluginu
	 * @return string -- název adresáře
	 */
//	final public function getDir(){
//      //odstranění přívlastku JsPlugin
//      $jsDir = str_ireplace(__CLASS__.'_', '', $this->jsPluginName);
//      return '.'.URL_SEPARATOR.self::JSPLUGINS_BASE_DIR.URL_SEPARATOR
//				.strtolower($jsDir).URL_SEPARATOR;
//	}

   /**
    * Metoda vrací virtuální složku pro jsplugin, je využito při generování dynamických
    * souborů
    *
    * @return string -- virtuální adresář se souborem
    */
//   final public function getVirtualDir(){
////      $dir = '.'.URL_SEPARATOR.self::VIRTUAL_DIR_PREFIX.strtolower($this->jsPluginName).URL_SEPARATOR;
//      $dir = '.'.URL_SEPARATOR.strtolower($this->jsPluginName).URL_SEPARATOR;
//		return $dir;
//   }
	
	/**
	 * Metoda nastaví zadaný konfigurační parametr pluginu
	 *
	 * @param string -- název parametru
	 * @param mixed -- hodnota paramettru
	 */
	final public function setCfgParam($paramName, $paramValue) {
		$this->config[$paramName] = $paramValue;
	}

	/**
	 * Metoda vrací zadaný konfigurační parametr pluginu
	 *
	 * @param string -- název parametru
	 * @return mixed -- hodnota parametru
	 */
	final protected function getCfgParam($paramName) {
		if(isset($this->config[$paramName])){
			return $this->config[$paramName];
		} else {
			return null;
		}
	}
		
	/**
	 * Metoda vrací všechny konfigurační parametry parametr
	 *
	 * @return array -- pole s parametry
	 */
	final protected function getAllCfgParams() {
		return $this->config;
	}
	
	/**
	 * metoda odešla obsah na výstup a ukončí script
	 * @param string -- obsah souboru
	 */
//	final protected function sendFileContent($content) {
//		header("Content-Length: " . strlen($content));
//		header("Content-type: application/x-javascript");
//		echo $content;
//		exit();
//	}
}
?>