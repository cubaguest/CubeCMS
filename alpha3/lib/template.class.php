<?php
/**
 * Třída pro práci s šablonami modulů
 *
 */
class Template {
	/**
	 * Název pole s moduly (items) kategorie
	 * @var array
	 */
	const ITEMS_ARRAY_NAME = 'items';
	
	/**
	 * Konstanta s názvem pole s šablonami
	 * @var string
	 */
	const TEMPLATE_ARRAY_NAME = 'templates';

	/**
	 * Konstanta s názvem pole s css styly
	 * @var string
	 */

	const STYLESHHETS_ARRAY_NAME = 'stylesheets';
	
	/**
	 * Konstanta s názvem pole s javascripty
	 * @var string
	 */
	const JAVASCRIPTS_ARRAY_NAME = 'javascripts';
	
	/**
	 * Konstanta s názvem pole s proměnými
	 * @var string
	 */
	const VARIABLES_ARRAY_NAME = 'VARS';

	/**
	 * Proměná obsahuje pole se všemi proměnými šablony
	 * @var array
	 */
	private static $tplVars = array();
	
	/**
	 * Statické pole s šablonami
	 * @var array
	 */
	private static $items = array();
	
	/**
	 * Statické pole s css styly
	 * @var array
	 */
	private static $stylesheets = array();
	
	/**
	 * Statické pole s javascript soubory
	 * @var array
	 */
	private static $javascripts = array();
	
	/**
	 * Proměná s id modulu (item)
	 * @var integer
	 */
	private $module = null;

	
	/*
	 * ========== METODY
	 */
	
	/**
	 * Konstruktor třídy
	 *
	 * @param Module -- objekt modulu
	 */
	function __construct(Module $module){
		$this->module = $module;
	}
	
	/**
	 * Metoda vrací objekt modulu
	 * @return Module -- objekt modulu
	 */
	private function getModule() {
		return $this->module;
	}
	
	/**
	 * metoda přidává zadanou šablonu do výstupu
	 * 
	 * @param string -- název šablony
	 * @param boolean -- true pokud má být použita systémová šablona
	 */
	public function addTpl($tplName, $engineTpl = false){
		$this->checkItemTplArray();
		
		//TODO kontrola souborů
		//přidání šablony do pole s šablonami modulu
		if($engineTpl == false){
			array_push(self::$items[$this->getModule()->getId()][self::TEMPLATE_ARRAY_NAME],
				   $this->getModule()->getDir()->getTemplatesDir().$tplName);
		} else {
			array_push(self::$items[$this->getModule()->getId()][self::TEMPLATE_ARRAY_NAME],
				   AppCore::getAppWebDir().DIRECTORY_SEPARATOR.AppCore::TEMPLATES_DIR.DIRECTORY_SEPARATOR.$tplName);
		}
		
	}
	
	/**
	 * Metody kontroluje vytvoření pole s moduly (items) kategorie
	 */
	private function checkItemArray(){
		if(!isset(self::$items[$this->getModule()->getId()])){
			self::$items[$this->getModule()->getId()] = array();
		}
	}
	
	/**
	 * Metoda kontroluje pole s šablonami pro modul
	 */
	private function checkItemTplArray() {
//		Kontrola hlavního pole
		$this->checkItemArray();
		
		if(!isset(self::$items[$this->getModule()->getId()][self::TEMPLATE_ARRAY_NAME])){
			self::$items[$this->getModule()->getId()][self::TEMPLATE_ARRAY_NAME] = array();
		}
	}
	
	/**
	 * metoda přidává zadany css styl do výstupu
	 * @param string -- název scc stylu
	 * @param boolean -- true pokud je zadána i cesta se souborem
	 */
	public function addCss($cssName, $withPath = false){
		//TODO kontrola souborů a duplicit
		if(!$withPath){
			$cssName = $this->module->getDir()->getStylesheetsDir().$cssName;
		}
		array_push(self::$stylesheets, $cssName);
	}
	
	/**
	 * metoda přidává zadaný javascript do výstupu
	 * @param string -- název javascriptu
	 * @param boolean -- true pokud je zadána i cesta se souborem
	 */
	public function addJS($javaScriptName, $withPath = false){
		//TODO kontrola souborů a duplicit
		if(!$withPath){
			$javaScriptName = $this->module->getDir()->getJavaScriptsDir().$javaScriptName;
		}
		array_push(self::$javascripts, $javaScriptName);
	}
	
	/**
	 * statická metoda vrací pole se styly
	 * @return array -- pole se styly (obsahuje i cestu)
	 */
	public static function getStylesheets() {
		return self::$stylesheets;
	}
	
	/**
	 * statická metoda vrací pole s javascripty
	 * @return array -- pole s javascripty (obsahuje i cestu)
	 */
	public static function getJavaScripts() {
		return self::$javascripts;
	}
	
	/**
	 * statická metoda vrací pole s šabloname modulů (items) kategorií
	 * @return array -- pole s šablonami a proměnými modulů (items)
	 */
	public static function getCategoryItems() {
		return self::$items;
	}
	
	/**
	 * statická metoda vrací pole s proměnými 
	 * @return array -- pole s proměnými
	 */
	public static function getMainVars() {
		return self::$tplVars;
	}
	
	
	/**
	 * Statická metoda vloží proměnou do šablony
	 * @param string -- název proměnné
	 * @param string/array -- hodnota proměnné
	 */
	public static function addMainVar($varName, $varValue, $arrayName = null){
		if($arrayName == null){
			self::$tplVars[$varName] = $varValue;
		} else {
			if(!isset(self::$tplVars[$arrayName])){
				self::$tplVars[$arrayName] = array();
			}
			self::$tplVars[$arrayName][$varName] = $varValue;
		}
	}
	
	/**
	 * Metoda přiřazuje proměnné do šablony
	 * @param string -- název proměnné
	 * @param string/array -- hodnota proměnné
	 * @param boolean -- true pokud má být proměná zařazena do modulu (default: true)
	 */
	public function addVar($varName, $varValue, $isModuleVar = true) {
		$this->checkItemVarArray();
		
		if($isModuleVar){
			self::$items[$this->getModule()->getId()][self::VARIABLES_ARRAY_NAME][$varName] = $varValue;
		} else {
			self::$items[$varName] = $varValue;
		}
		
	}
	
	/**
	 * Metody zkontroluje vytvoření pole s proměnnými v šabloně
	 */
	private function checkItemVarArray() {
//		kontrola hlavního pole
		$this->checkItemArray();
		
		if(!isset(self::$items[$this->getModule()->getId()][self::VARIABLES_ARRAY_NAME])){
			self::$items[$this->getModule()->getId()][self::VARIABLES_ARRAY_NAME] = array();
		}
	}
	
	/**
	 * Metoda přidává zadaný JsPlugin do šablony
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
					$this->addJS($jsPlugin->getJsPluginDir().$jsFile, true);
				}
			}
//			Vložení ostatních css souborů pluginu
			$cssOtherFiles = $jsPlugin->getAllCssFiles();
			if(!empty($cssOtherFiles)){
				foreach ($cssOtherFiles as $cssFile) {
					$this->addCss($jsPlugin->getJsPluginDir().$cssFile, true);
				}
			}
			
//			Přidání JS souboru s nastavením
			if($jsPlugin->isDefaultJsSettings() AND $jsPlugin->getSettingsJsFile() != null){
				$this->addJS($jsPlugin->getJsPluginDir().$jsPlugin->getSettingsJsFile(), true);
			} else if($jsPlugin->getSettingsJsFile() != null) {
				$this->addJS($jsPlugin->getSettingsJsFile());
			}
//			Přidání CSS souboru s nastavením
			if($jsPlugin->isDefaultCssSettings() AND $jsPlugin->getSettingsCssFile() != null){
				$this->addCss($jsPlugin->getJsPluginDir().$jsPlugin->getSettingsCssFile(), true);
			} else if($jsPlugin->getSettingsCssFile() != null){
				$this->addCss($jsPlugin->getSettingsCssFile());
			}
		} else {
			new CoreException(_("Nebyl vložen objekt JsPluginu"), 1);
		}
	}
	
}

?>