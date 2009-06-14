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
	 * Název proměné poocí které se přenáší název souboru
	 * @var string
	 */
	const URL_FILE_NAME = 'file';

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
	 * Výchozí js soubor s nastavení js pluginu
	 * @var string
	 */
	private $settingsJsFile = null;
	
	/**
	 * Proměná obsahuje je-li použit výchozí js soubor s nastavením
	 * @var boolean
	 */
	private $settingsJsFileIsDefault = true;
	
	/**
	 * Výchozí css soubor s nastavením js pluginu
	 * @var string 
	 */
	private $settingsCssFile = null;

	/**
	 * Proměná obsahuje je-li použit výchozí css soubor s nastavením
	 * @var boolean
	 */
	private $settingsCssFileIsDefault = true;
		
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
	 * Konstruktor třídy
	 */
	final public function __construct(){
      $this->jsPluginName = get_class($this);
      $this->initJsPlugin();
      // Pokud je zpracováván virtuální soubor JsPluginu
      if(UrlRequest::isSupportedServices()){
         $file = new JsPlugin_JsFile(rawurldecode(UrlRequest::getSupportedServicesFile()), true);
         // generování obsahu souboru
         $file->setParams(UrlRequest::getSupportedServicesParams());
      	$this->generateFile($file);
		} else {
			$this->initFiles();
		}

	}
	
	/**
	 * Třída, která se provede při inicializaci pluginu
	 */
	protected abstract  function initJsPlugin();
	
	/**
	 * Metoda se využívá pro načtení proměných do stránky, 
	 * je volána při volání parametru stránky pro JsPlugin
	 * a je pouze zpracována tato metoda (generování nastavení atd)
	 */
	protected abstract function generateFile(JsPlugin_JsFile $file);
	
	/**
	 * Metoda inisializuje všechny soubory, se kterými JsPlugin pracuje
	 */
	protected  abstract function initFiles();
	
	/**
	 * Metoda nastavuje js soubor s nasatvením pluginu
	 * @param JsPlugin_JsFile -- název js souboru (je umístěn v adresáři modulu)
	 */
	final protected function setSettingJsFile(JsPlugin_JsFile $jsFile) {
		$this->settingsJsFile = $jsFile;
	}
	
	/**
	 * Metoda nastavuje css soubor, který se načte místo výchozího
	 * @param string -- název css souboru (je umístěn v adresáři modulu)
	 */
	final protected function setSettingCssFile($cssFile) {
		$this->settingsCssFile = $cssFile;
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
	 * @param boolean $withPaths -- jestli mají být vloženy i cesty
    * @return array -- pole js souborů
	 */
	final public function getAllJsFiles($withPaths = true){
      $filesArray = array();
      //			Vložení ostatních js souborů pluginu
      if(!empty($this->jsFilesArray)) {
         foreach ($this->jsFilesArray as $jsFile) {
            if($withPaths AND !ereg('^./'.self::JSPLUGINS_BASE_DIR.'/(.*)$', $jsFile)){
               if($jsFile->isVirtual()){
                  array_push($filesArray, $this->getVirtualDir().$jsFile);
               } else {
                  array_push($filesArray, $this->getDir().$jsFile);
               }
            } else {
               array_push($filesArray, $jsFile);
            }
         }
      }
      if($this->getSettingsJsFile() != null) {
       if($this->getSettingsJsFile()->isVirtual()) {
            array_push($filesArray, $this->getVirtualDir().$this->getSettingsJsFile());
         } else {
            array_push($filesArray, $this->getDir().$this->getSettingsJsFile());
         }
      }
      return $filesArray;
	}
	
	/**
	 * Metoda vrací pole všech Css souborů pluginu
	 *@return array -- pole Css souborů
	 */
	final public function getAllCssFiles(){
      $filesArray = array();
		//			Vložení ostatních css souborů pluginu
      if(!empty($this->cssFilesArray)) {
         $cssFiles = $this->cssFilesArray;
         foreach ($cssFiles as $cssFile) {
            array_push($filesArray, $this->getDir().$cssFile);
         }
      }
      //			Přidání CSS souboru s nastavením
      if($this->isDefaultCssSettingsFile() AND $this->getSettingsCssFile() != null) {
         array_push($filesArray, $this->getDir().$this->getSettingsCssFile());
      } else if($this->getSettingsCssFile() != null) {
         array_push($filesArray, $this->getSettingsCssFile());
      }
      return $filesArray;
	}
	
	/**
	 * Metoda nastaví nový výchozí js soubor nastavení
	 * @param JsPlugin_JsFile -- název js souboru
	 */
	final public function setDefJsFile(JsPlugin_JsFile $jsFile){
		$this->settingsJsFileIsDefault = false;
		$this->setSettingJsFile($jsFile);
	}
	
	/**
	 * Metoda nastaví nový výchozí css soubor nastavení
	 * @param string -- název css souboru
	 */
	final public function setDefCssFile($cssFile){
		$this->settingsCssFileIsDefault = false;
		$this->setSettingCssFile($cssFile);
	}
	
	/**
	 * Metoda nastaví název js pluginu -- je totžný s názvem adresáře (case insensitive)
	 * @param string -- název JsPluginu
	 */
//	final protected function setJsPluginName($pluginName){
//		$this->jsPluginName = $pluginName;
//	}
	
	/**
	 * Metoda vrací název adresáře pluginu
	 * @return string -- název adresáře
	 */
	final public function getDir(){
      //odstranění přívlastku JsPlugin
      $jsDir = str_ireplace(__CLASS__.'_', '', $this->jsPluginName);
      return '.'.URL_SEPARATOR.self::JSPLUGINS_BASE_DIR.URL_SEPARATOR
				.strtolower($jsDir).URL_SEPARATOR;
	}

   /**
    * Metoda vrací virtuální složku pro jsplugin, je využito při generování dynamických
    * souboru
    *
    * @return string -- virtuální adresář se souborem
    */
   final public function getVirtualDir(){
//      $dir = '.'.URL_SEPARATOR.self::VIRTUAL_DIR_PREFIX.strtolower($this->jsPluginName).URL_SEPARATOR;
      $dir = '.'.URL_SEPARATOR.strtolower($this->jsPluginName).URL_SEPARATOR;
		return $dir;
   }
	
	/**
	 * Metoda vrací true pokud se použije výchozí js soubr s nastavením JsPluginu
	 * @return boolean -- true pro výchozí nastavení
	 */
	final public function isDefaultJsSettingsFile(){
      return $this->settingsJsFileIsDefault;
	}
	
	/**
	 * Metoda vrací true pokud se použije výchozí css soubr s nastavením JsPluginu
	 * @return boolean -- true pro výchozí nastavení
	 */
	final public function isDefaultCssSettingsFile(){
		return $this->settingsCssFileIsDefault;
	}
	
	/**
	 * Metoda vrací název výchozího js souboru s nastavením
	 * @return JsPlugin_JsFile -- název js souboru
	 */
	final public function getSettingsJsFile(){
		return $this->settingsJsFile;
	}
	
	/**
	 * Metoda vrací název výchozího css souboru s nastavením
	 * @return string -- název css souboru
	 */
	final public function getSettingsCssFile(){
		return $this->settingsCssFile;
	}
	
	/**
	 * Metoda nastaví zadaný parametr pluginu
	 *
	 * @param string -- název parametru
	 * @param mixed -- hodnota paramettru
	 */
	final public function setPluginParam($paramName, $paramValue) {
		$this->pluginParams[$paramName] = $paramValue;
	}

	/**
	 * Metoda vrací zadaný parametr pluginu
	 *
	 * @param string -- název parametru
	 * @return mixed -- hodnota parametru
	 */
	final protected function getPluginParam($paramName) {
		if(isset($this->pluginParams[$paramName])){
			return $this->pluginParams[$paramName];
		} else {
			return null;
		}
	}
		
	/**
	 * Metoda vrací všechny parametry parametr
	 *
	 * @return array -- pole s parametry
	 */
	final protected function getAllPluginParams() {
		return $this->pluginParams;
	}
	
	/**
	 * metoda odešla obsah na výstup a ukončí script
	 * @param string -- obsah souboru
	 */
	final protected function sendFileContent($content) {
		header("Content-Length: " . strlen($content));
		header("Content-type: application/x-javascript");
		echo $content;
		exit();
	}
}
?>