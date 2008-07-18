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
	 * Stzatické pole s šablonami
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
	 */
	public function addCss($cssName, $globalSytle = false){
		//TODO kontrola souborů a duplicit
		if(!$globalSytle){
			$cssName = $this->module->getDir()->getStylesheetsDir().$cssName;
		} else {
			//TODO přidat vkládání css souborů z enginu
		}
		
		array_push(self::$stylesheets, $cssName);
	}
	
	/**
	 * metoda přidává zadaný javascript do výstupu
	 * @param string -- název javascriptu
	 */
	public function addJS($javaScriptName, $globalJS = false){
		//TODO kontrola souborů a duplicit
		if(!$globalJS){
			$javaScriptName = $this->module->getDir()->getJavaScriptsDir().$javaScriptName;
		} else {
			//TODO přidat podporu pro vkládání javascriptů z enginu
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
	 * Metoda přiřazuje proměnné do šablony
	 * @param string -- název proměnné
	 * @param string/array -- hodnota proměnné
	 */
	public function addVar($varName, $varValue) {
		$this->checkItemVarArray();
		
		self::$items[$this->getModule()->getId()][self::VARIABLES_ARRAY_NAME][$varName] = $varValue;
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
	
}

?>