<?php
//require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'template.class.php');


abstract class View {
	/**
	 * Adresář s JavaScript Pluginy
	 * @var string
	 */
	const JSPLUGINS_DIR = 'JsPlugins';
	
	/**
	 * Objekt modelu aplikace
	 */
	private $model = null;

	/**
	 * Objekt s konfigurací
	 * @var Config
	 */
	private $config = null;

	/**
	 * Objekt s informacemi o modulu
	 * @var Module
	 */
	private $module = null;

	/**
	 * Objekt pro práci s šablonovacím systémem
	 * @var 
	 */
	private $template = null;
	
	/**
	 * Konstruktor Viewu
	 *
	 * @param Model -- použitý model
	 * @param Config -- konfigurační volby
	 */
	function __construct(Models $model, Module $module, Config $config) {
		/**
		 * Funkce slouží pro automatické načítání potřebných tříd
		 * @param string -- název třídy
		 */
//		function __autoload($className){
//			//		Zmenšení na malá písmena
//			$className = strtolower($className);
//			//je načítána hlavní knihovna
//			if(file_exists('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. $className . '.class.php')){
//				require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. $className . '.class.php');
//			}
//			//je načítán e-plugin
//			else if(file_exists('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. self::JSPLUGINS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php')) {
//				require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. self::JSPLUGINS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php');
//			}else {
//				new CoreException(_("Nepodařilo se načíst potřebnou třídu ").$className, 2);
//			}
//		}	
		
		
		$this->model = $model;
		$this->config = $config;
		$this->module = $module;
	}

	/**
	 * Hlavní abstraktní třída pro vytvoření pohledu
	 */
	abstract function mainView();

	final public function getModule() {
		return 	$this->module;
	}
	
	
	/**
	 * Funkce vrací datový adresář modulu
	 * @return string -- datový adresář modulu
	 */
	final public function getDataDir() {
		return $this->getModule()->getDir()->getDataDir();
	}
	
	/**
	 * Funkce vrací objekt modelu
	 * @return Models -- objekt modelu
	 */
	final public function getModel() {
		return $this->model;
	}
	
	/**
	 * Metoda vrací objekt šablony, přes kerý se přiřazují proměnné do šablony
	 * @return Template -- objekt šablony
	 */
	final public function template(){
		if($this->template == null){
			return $this->template = new Template($this->getModule());
		} else {
			return $this->template;
		}
	}
	
	/**
	 * Metoda přidává zadaný JsPlugin do pohledu
	 * 
	 * @param JsPlugin -- objekt js pluginu
	 */
	final public function addJsPlugin(JsPlugin $jsPlugin)
	{
		if(get_parent_class($jsPlugin) == 'JsPlugin'){
//			Vložení ostatních js souborů pluginu 
			$jsOtherFiles = $jsPlugin->getAllJsFiles();
			if(!empty($jsOtherFiles)){
				foreach ($jsOtherFiles as $jsFile) {
					$this->template()->addJS($jsPlugin->getJsPluginDir().$jsFile, true);
				}
			}
//			Vložení ostatních css souborů pluginu
			$cssOtherFiles = $jsPlugin->getAllCssFiles();
			if(!empty($cssOtherFiles)){
				foreach ($cssOtherFiles as $cssFile) {
					$this->template()->addCss($jsPlugin->getJsPluginDir().$cssFile, true);
				}
			}
			
//			Přidání JS souboru s nastavením
			if($jsPlugin->isDefaultJsSettings() AND $jsPlugin->getSettingsJsFile() != null){
				$this->template()->addJS($jsPlugin->getJsPluginDir().$jsPlugin->getSettingsJsFile(), true);
			} else if($jsPlugin->getSettingsJsFile() != null) {
				$this->template()->addJS($jsPlugin->getSettingsJsFile());
			}
//			Přidání CSS souboru s nastavením
			if($jsPlugin->isDefaultCssSettings() AND $jsPlugin->getSettingsCssFile() != null){
				$this->template()->addCss($jsPlugin->getJsPluginDir().$jsPlugin->getSettingsCssFile(), true);
			} else if($jsPlugin->getSettingsCssFile() != null){
				$this->template()->addCss($jsPlugin->getSettingsCssFile());
			}
		} else {
			new CoreException(_("Nebyl vložen objekt JsPluginu"), 1);
		}
	}
}

?>