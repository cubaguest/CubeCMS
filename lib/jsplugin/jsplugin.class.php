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

abstract class JsPlugin extends TrObject {
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
	 * Pole obsahuje seznam souborů nutných pro načtení
	 * @var array
	 */
	private $filesArray = array();
	
	/**
	 * Název JsPluginu -- je totožný s adresářem js pluginu
	 * @var string
	 */
	protected $jsPluginName = null;
   
   protected $jsFilesDir = null;


   /**
	 * Pole s parametry JsPluginu
	 * @var array
	 */
   protected $pluginParams = array();
	
   /**
    * Pole s konfigurací pluginu
    * @var array
    */
   protected $config = array();

   /**
    * Objekt vybrané kategorie
    * @var Category
    */
   private $category = null;

	/**
	 * Konstruktor třídy
	 */
	final public function __construct(){
      $this->setJsPluginName(str_ireplace(__CLASS__.'_', '', get_class($this)));
      $this->jsFilesDir = $this->jsPluginName;
//      $this->setFiles();
      $this->initJsPlugin();
	}
	
   /**
    * Metoda nastaví název JsPluginu (nový, přepíše aktuální), pouze v případě nouze
    * @param string $name -- název JsPluginu používá se pro přístup k adresáři
    */
   public function setJsPluginName($name) {
      $this->jsPluginName = $name;
   }
   
   /**
    * Metoda nastaví adresář s js soubory JsPluginu
    * @param string $name -- název adresáře ve složce jscripts
    */
   public function setJsFilesDir($name) {
      $this->jsFilesDir = $name;
   }

   /**
    * Metoda nastaví název objekt kategorie
    * @param Category $category -- název JsPluginu používá se pro přístup k adresáři
    */
   public function setCategory(Category $category) {
      $this->category = $category;
   }

   /**
    * Metoda vrací objekt vybrané kategorie
    * @return Category
    */
   public function category(){
      if($this->category == null){
         return Category::getSelectedCategory();
      } else {
         return $this->category;
      }
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
         $isAction = false;
         if (method_exists($this, $actionName . 'Action')) {
            $this->{$actionName . 'Action'} ();
            $isAction = true;
         }
         
         $this->pluginParams = $params; /* tady se předávají parametry z url, nemělo by tu být spíš $_GET? */
         if (method_exists($this, $actionName . ucfirst($outputType) . 'View')) {
            $this->{$actionName . ucfirst($outputType) . 'View'} ();
         } else if (method_exists($this, $actionName . 'View')) {
            $this->{$actionName . 'View'} ();
         } else {
            if(!$isAction){
               throw new UnexpectedValueException(sprintf($this->tr('Neimplementována akce "%s" JsPluginu'), $actionName));
            }
            // pokud je výstup json a není funkce pro pohled, tak odešleme proměnné třídy
            if(strtolower($outputType) == 'json'){
               $respond = new XHR_Respond_VVEAPI();
               $respond->setData(array_diff_key(get_object_vars($this), get_class_vars(__CLASS__)));
               $respond->renderRespond();
            }
            // asi přidat implementaci také pro výstupy typu html a txt
         }
   }

	/**
	 * Metoda přídává css soubor js pluginu
	 * @param string -- název souboru nebo JsPlugin_File
	 */
	final protected function addFile($file){
      if($file instanceof JsPlugin_File AND $file->isVirtual() == true){
         $file->setPluginName($this->jsPluginName);
      } else if($file instanceof JsPlugin_File AND $file->isVirtual() == false){
         $file->setPluginName($this->jsFilesDir);
      }
		array_push($this->filesArray, $file);
	}
	
   /**
    * Metoda přidá JsPlugin do závislosti
    * @param JsPlugin $jsplugin -- objekt JsPluginu 
    */
   final protected function addDependJsPlugin(JsPlugin $jsplugin) {
      $file = $jsplugin->getAllFiles();
      foreach ($file as $f) {
         $this->addFile($f);
      }
   }

	/**
	 * Metoda vrací pole všech Js souborů pluginu i s cestami
    * @return array of JsPlugin_JsFile -- pole souborů
	 */
	final public function getAllFiles(){
      $addFiles = $this->filesArray;
      $this->filesArray = array();
      $this->setFiles();
      return array_merge($this->filesArray, $addFiles);
	}
	
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
    * Metoda vrací název jspluginu
    * @return string
    */
   public function getName(){
      return $this->jsPluginName;
   }
}
?>