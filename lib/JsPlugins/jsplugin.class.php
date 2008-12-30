<?php
/**
 * Abstraktní třída pro obsluh JavaScript Pluginů JsPlugins.
 * Třída slouží jako základ pro tvorbu JsPluginů a jejich implementaci. Poskytuje 
 * základní přístup k parametrům JsPlugin. Umožňuje také přímé generování souborů
 * pro tvorbu dynamických nastavení a obsahů. Vše je ovládáno přes poheld (viewer).
 * 
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: jsplugins.class.php 3.1.8 beta1 13.11.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
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
	 * Pole s parametry v url
	 * @var array
	 */
	private $urlProtectedParams = array(self::URL_FILE_NAME, 'jsplugin');
	
	/**
	 * Pole s vybrtanými parametry
	 * @var array
	 */
	private $fileParams = array();
	
	/**
	 * Vybraný soubor, který se má zpracovat
	 * @var string
	 */
	private $fileName = null;
	
	/**
	 * Konstruktor třídy
	 */
	final public function __construct(){
		$this->initJsPlugin();
        if(UrlRequest::isJsplugin()){
			$this->parseParams();
			$this->generateFile();
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
	protected  abstract function generateFile();
	
	/**
	 * Metoda inisializuje všechny soubory, se kterými JsPlugin pracuje
	 */
	protected  abstract function initFiles();
	
	/**
	 * Metoda parsuje parametry pro generování souboru
	 */
	final protected function parseParams() {
		$queryString = $_SERVER["QUERY_STRING"];
		if($queryString != null){
			$tmpParamsArray = array();
			$tmpParamsArray = explode(Links::URL_PARAMETRES_SEPARATOR_IN_URL, $queryString);
			foreach ($tmpParamsArray as $fullParam) {
				$tmpParam = explode(Links::URL_SEP_PARAM_VALUE, $fullParam);
					
				if(isset($tmpParam[0]) AND isset($tmpParam[1]) AND !in_array($tmpParam[0], $this->urlProtectedParams)){
					$this->fileParams[$tmpParam[0]] = rawurldecode($tmpParam[1]);
				}
			}
			$this->fileName = rawurldecode($_GET[self::URL_FILE_NAME]);
		};
	}
	 
	
	/**
	 * Metoda nastavuje js soubor, který se načte místo výchozího
	 * @param JsPluginJsFile -- název js souboru (je umístěn v adresáři modulu)
	 */
	final protected function setSettingJsFile(JsPluginJsFile $jsFile) {
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
	 * @param JsPluginJsFile -- název js souboru
	 */
	final protected function addJsFile(JsPluginJsFile $jsFile)
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
	 * @param JsPluginJsFile -- název js souboru
	 */
	final public function setDefJsFile(JsPluginJsFile $jsFile)
	{
		$this->settingsJsFileIsDefault = false;
		$this->setSettingJsFile($jsFile);
	}
	
	/**
	 * Metoda nastaví nový výchozí css soubor nastavení
	 * @param string -- název css souboru
	 */
	final public function setDefCssFile($cssFile)
	{
		$this->settingsCssFileIsDefault = false;
		$this->setSettingCssFile($cssFile);
	}
	
	/**
	 * Metoda nastaví název js pluginu -- je totžný s názvem adresáře (case insensitive)
	 * @param string -- název JsPluginu
	 */
	final protected function setJsPluginName($pluginName)
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
	final public function isDefaultJsSettingsFile()
	{
		return $this->settingsJsFileIsDefault;
	}
	
	/**
	 * Metoda vrací true pokud se použije výchozí css soubr s nastavením JsPluginu
	 * @return boolean -- true pro výchozí nastavení
	 */
	final public function isDefaultCssSettingsFile()
	{
		return $this->settingsCssFileIsDefault;
	}
	
	/**
	 * Metoda vrací název výchozího js souboru s nastavením
	 * @return JsPluginJsFile -- název js souboru
	 */
	final public function getSettingsJsFile()
	{
		$file = $this->settingsJsFile;
		
		if(!empty($this->pluginParams)){
			$params = http_build_query($this->pluginParams);
			$file.='?'.$params;
		}
		
		return $file;
//		return $this->settingsJsFile.;
	}
	
	/**
	 * Metoda vrací název výchozího css souboru s nastavením
	 * @return string -- název css souboru
	 */
	final public function getSettingsCssFile()
	{
		return $this->settingsCssFile;
	}
	
	/**
	 * Metoda nastaví zadaný parametr pluginu
	 *
	 * @param string -- název parametru
	 * @param mixed -- hodnota paramettru
	 */
	final public function setParam($paramName, $paramValue) {
		$this->pluginParams[$paramName] = $paramValue;
	}

	/**
	 * Metoda vrací zadaný parametr pluginu
	 *
	 * @param string -- název parametru
	 * @return mixed -- hodnota parametru
	 */
	final protected function getParam($paramName) {
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
	final protected function getAllParams() {
		return $this->pluginParams;
	}
	
	/**
	 * Metoda vrací předané parametry v url souboru
	 * @return array
	 */
	final protected function getFileParams() {
		return $this->fileParams;
	}

	/**
	 * Metoda vrací předaný parametr v url souboru
	 * @param string -- název parametru
	 * @return mixed -- hodnota parametru
	 */
	final protected function getFileParam($paramName) {
		if(isset($this->fileParams[$paramName])){
			return $this->fileParams[$paramName];
		} else {
			return null;
		}
	}
	
	/**
	 * Metoda vrací název zpracovávaného souboru
	 * @return string -- název souboru
	 */
	final protected function getFileName() {
		return $this->fileName;
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