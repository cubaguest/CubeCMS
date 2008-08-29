<?php
/**
 * Abstraktní třída pro obsluh JavaScript Pluginů
 * 
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	JsPlugins class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: jsplugins.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Abstraktní třída pro práci s JsPluginy
 */
abstract class JsPlugin {
	/**
	 * Konstanta s název adresáře JsPluginů
	 * @var string
	 */
	const JSPLUGINS_BASE_DIR = 'jscripts';
	
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
	 * Konstruktor třídy
	 */
	final public function __construct(){
		$this->initJsPlugin();
	}
	
	/**
	 * Třída, která se provede při inicializaci pluginu
	 */
	abstract protected function initJsPlugin();
	
	/**
	 * Metoda nastavuje js soubor, který se načte místo výchozího
	 * @param string -- název js souboru (je umístěn v adresáři modulu)
	 */
	
	final protected function setDefaultSettingJsFile($jsFile) {
		$this->settingsJsFile = $jsFile;
	}
	
	/**
	 * Metoda nastavuje css soubor, který se načte místo výchozího
	 * @param string -- název css souboru (je umístěn v adresáři modulu)
	 */
	
	final protected function setDefaultSettingCssFile($cssFile) {
		$this->settingsCssFile = $cssFile;
	}
	
	/**
	 * Metoda přídává js soubor se scriptem
	 * @param string -- název js souboru
	 */
	final protected function addJsFile($jsFile)
	{
		array_push($this->jsFilesArray, $jsFile);
	}
	
	/**
	 * Metoda přídává css soubor js pluginu
	 * @param string -- název css souboru
	 */
	final protected function addCssFile($cssFile)
	{
		array_push($this->cssFilesArray, $cssFile);
	}
	
	/**
	 * Metoda vrací pole všech Js souborů pluginu
	 *@return array -- pole js souborů
	 */
	final public function getAllJsFiles()
	{
		return $this->jsFilesArray;
	}
	
	/**
	 * Metoda vrací pole všech Css souborů pluginu
	 *@return array -- pole Css souborů
	 */
	final public function getAllCssFiles()
	{
		return $this->cssFilesArray;
	}
	
	/**
	 * Metoda nastaví nový výchozí js soubor nastavení
	 * @param string -- název js souboru
	 */
	final public function setDefJsFile($jsFile)
	{
		$this->settingsJsFileIsDefault = false;
		$this->setDefaultSettingJsFile($jsFile);
	}
	
	/**
	 * Metoda nastaví nový výchozí css soubor nastavení
	 * @param string -- název css souboru
	 */
	final public function setDefCssFile($cssFile)
	{
		$this->settingsCssFileIsDefault = false;
		$this->setDefaultSettingCssFile($cssFile);
	}
	
	/**
	 * Metoda nastaví název js pluginu -- je totžný s názvem adresáře (case insensitive)
	 * @param string -- název JsPluginu
	 */
	final public function setJsPluginName($pluginName)
	{
		$this->jsPluginName = $pluginName;
	}
	
	/**
	 * Metoda vrací název adresáře pluginu
	 * @return string -- název adresáře
	 */
	final public function getJsPluginDir()
	{
		$dir = '.'.ModuleDirs::DIR_SEPARATOR.self::JSPLUGINS_BASE_DIR.ModuleDirs::DIR_SEPARATOR
				.strtolower($this->jsPluginName).ModuleDirs::DIR_SEPARATOR;
				
		return $dir;
	}
	
	
	/**
	 * Metoda vrací true pokud se použije výchozí js soubr s nastavením JsPluginu
	 * @return boolean -- true pro výchozí nastavení
	 */
	final public function isDefaultJsSettings()
	{
		return $this->settingsJsFileIsDefault;
	}
	
	/**
	 * Metoda vrací true pokud se použije výchozí css soubr s nastavením JsPluginu
	 * @return boolean -- true pro výchozí nastavení
	 */
	final public function isDefaultCssSettings()
	{
		return $this->settingsCssFileIsDefault;
	}
	
	/**
	 * Metoda vrací název výchozího js souboru s nastavením
	 * @return string -- název js souboru
	 */
	final public function getSettingsJsFile()
	{
		return $this->settingsJsFile;
	}
	
	/**
	 * Metoda vrací název výchozího css souboru s nastavením
	 * @return string -- název css souboru
	 */
	final public function getSettingsCssFile()
	{
		return $this->settingsCssFile;
	}
}

?>