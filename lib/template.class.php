<?php
/**
 * Třída pro práci s šablonami modulů
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	Template class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: template.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Třída pro obsluhu šablony
 */
class Template {
	/**
	 * Název pole s moduly (items) kategorie
	 * @var array
	 */
	const ITEMS_ARRAY_NAME = 'ITEMS';
	
	/**
	 * Konstanta s názvem pole s šablonami
	 * @var string
	 */
	const TEMPLATES_ARRAY_NAME = 'TEMPLATES';

	/**
	 * Konstanta s názvem pole s css styly
	 * @var string
	 */

	const STYLESHHETS_ARRAY_NAME = 'STYLESHEETS';
	
	/**
	 * Konstanta s názvem pole s javascripty
	 * @var string
	 */
	const JAVASCRIPTS_ARRAY_NAME = 'JAVASCRIPTS';
	
	/**
	 * Konstanta s názvem pole s proměnými
	 * @var string
	 */
	const VARIABLES_ARRAY_NAME = 'VARS';

	/**
	 * Název proměné s id šablony
	 * @var string
	 */
	const TEMPLATE_ID_NAME = 'ID';
	
	/**
	 * Název proměné se souborem šablony
	 * @var string
	 */
	const TEMPLATE_FILE_NAME = 'FILE';
	
	/**
	 * Název proměné s Názvem Modulu
	 * @var string
	 */
	const TEMPLATE_MODULE_LABEL = 'LABEL';

	/**
	 * Název proměné s podnázvemnázvem Modulu
	 * @var string
	 */
	const TEMPLATE_MODULE_SUBLABEL = 'SUBLABEL';

	/**
	 * Název proměné s popisem Modulu
	 * @var string
	 */
	const TEMPLATE_MODULE_ALT = 'ALT';

	/**
	 * Název proměné s linkem na kategorii
	 * @var string
	 */
	const TEMPLATE_CATEGORY_LINK = 'LINK';

	/**
	 * Název proměné s idetifikátorem šablony modulu
	 * @var string
	 */
	const TEMPLATE_MODULE_STYLE_IDENT = 'IDENT';
	
	/**
	 * Pole s šablonami a proměnými modulu
	 * @var array
	 */
	private $templates = array();
	
	/**
	 * Pole s proměnýmy madulu předaných do neginu
	 * @var array
	 */
	private $engineVars = array();
	
	/**
	 * Proměná s názvem titulku okna
	 * @var string
	 */
	private $pageTitle = null;
	
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
	function __construct(){
//		$this->module = $module;
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
	 * @param string/array -- název šablony
	 * @param boolean -- true pokud má být použita systémová šablona
	 */
	public function addTpl($tplName, $engineTpl = false, $tplId = 1){
		$this->checkTemplatesArray();

		//TODO kontrola souborů
		//přidání šablony do pole s šablonami modulu
		if($this->getModule() != null){
			if($engineTpl == false){
				if(!is_array($tplName)){
					array_push($this->templates[$this->getModule()->getId()][self::TEMPLATES_ARRAY_NAME],
						array(self::TEMPLATE_FILE_NAME => $this->selectModuleTemplateFaceFile($tplName),
					  	self::TEMPLATE_ID_NAME =>  $tplId));
				} else {
					foreach ($tplName as $tpl) {
						array_push($this->templates[$this->getModule()->getId()][self::TEMPLATES_ARRAY_NAME],
							array(self::TEMPLATE_FILE_NAME => $this->selectModuleTemplateFaceFile($tpl),
					  		self::TEMPLATE_ID_NAME =>  $tplId));
					}
					
				}
			} else {
				if(!is_array($tplName)){
					array_push($this->templates[$this->getModule()->getId()][self::TEMPLATES_ARRAY_NAME],
//						array(self::TEMPLATE_FILE_NAME => AppCore::getAppWebDir().DIRECTORY_SEPARATOR.AppCore::TEMPLATES_DIR.DIRECTORY_SEPARATOR.$tplName,
						array(self::TEMPLATE_FILE_NAME => $this->selectGlobalTemplateFaceFile($tplName),
					  	self::TEMPLATE_ID_NAME =>  $tplId));
				} else {
					foreach ($tplName as $tpl) {
						array_push($this->templates[$this->getModule()->getId()][self::TEMPLATES_ARRAY_NAME],
//						array(self::TEMPLATE_FILE_NAME => AppCore::getAppWebDir().DIRECTORY_SEPARATOR.AppCore::TEMPLATES_DIR.DIRECTORY_SEPARATOR.$tplName,
							array(self::TEMPLATE_FILE_NAME => $this->selectGlobalTemplateFaceFile($tpl),
					  		self::TEMPLATE_ID_NAME =>  $tplId));
					}
						  	
				}
					  	
			}
		}
		
	}
	
	/**
	 * Metoda zjistí jestli šablona modulu existuje pro zadaný vzhled, a podle něj ji vrátí
	 */
	private function selectModuleTemplateFaceFile($file) {
//		echo AppCore::getTepmlateFaceDir().$this->getModule()->getDir()->getTemplatesDir(false).$file. '<br />';
//		zvolení vzhledu
//		vybraný vzhled
		if(file_exists(AppCore::getTepmlateFaceDir().$this->getModule()->getDir()->getTemplatesDir(false).$file)){
			$faceFile = AppCore::getTepmlateFaceDir().$this->getModule()->getDir()->getTemplatesDir(false).$file;
		} 
//		Výchozí vzhled
		else if(file_exists(AppCore::getTepmlateDefaultFaceDir().$this->getModule()->getDir()->getTemplatesDir(false).$file)){
			$faceFile = AppCore::getTepmlateDefaultFaceDir().$this->getModule()->getDir()->getTemplatesDir(false).$file;
		} 
//		Vzhled v engine
		else {
			$faceFile = $this->getModule()->getDir()->getTemplatesDir().$file;
		};
		
		return $faceFile;
	}

	/**
	 * Metoda zjistí jestli globální šablona existuje pro zadaný vzhled, a podle něj ji vrátí
	 */
	private function selectGlobalTemplateFaceFile($file) {
		
//		zvolení vzhledu
//		vybraný vzhled
		if(file_exists(AppCore::getTepmlateFaceDir().AppCore::TEMPLATES_DIR.DIRECTORY_SEPARATOR.$file)){
			$faceFile = AppCore::getTepmlateFaceDir().AppCore::TEMPLATES_DIR.DIRECTORY_SEPARATOR.$file;
		} 
//		Výchozí vzhled
		else if(file_exists(AppCore::getTepmlateDefaultFaceDir().AppCore::TEMPLATES_DIR.DIRECTORY_SEPARATOR.$file)){
			$faceFile = AppCore::getTepmlateDefaultFaceDir().AppCore::TEMPLATES_DIR.DIRECTORY_SEPARATOR.$file;
		} 
//		Vzhled v engine
		else {
//			$faceFile = $this->getModule()->getDir()->getTemplatesDir().$file;
			$faceFile = $file;
		};
		
		return $faceFile;
	}
	
	
	
	
	/** 
	 * Metody kontroluje vytvoření pole s moduly (items) kategorie
	 */
	private function checkTemplatesArray(){
		
		if($this->getModule() != null){
			if(!isset($this->templates[$this->getModule()->getId()])){
				$this->templates[$this->getModule()->getId()] = array();
				$this->templates[$this->getModule()->getId()][self::TEMPLATES_ARRAY_NAME] = array();
			}
		}
	}
	
	/**
	 * Metoda kontroluje pole s šablonami pro modul
	 */
//	private function checkTplArray() {
////		Kontrola hlavního pole
//		$this->checkTemplatesArray();
//		
//		if($this->getModule() != null){
//			if(!isset($this->items[$this->getModule()->getId()][self::TEMPLATE_ARRAY_NAME])){
//				$this->items[$this->getModule()->getId()][self::TEMPLATE_ARRAY_NAME] = array();
//			}
//		}
//	}
	
	/**
	 * metoda přidává zadany css styl do výstupu
	 * @param string -- název scc stylu
	 * @param boolean -- true pokud je zadána i cesta se souborem
	 */
	public function addCss($cssName, $withPath = false){
		//TODO kontrola souborů a duplicit
		if(!$withPath){
			if($this->getModule() != null){
				$cssName = $this->selectModuleStylesheetFaceFile($cssName);			
//				$cssName = $this->module->getDir()->getStylesheetsDir().$cssName;
			}
		}
		array_push(self::$stylesheets, $cssName);
	}
	
	/**
	 * Metoda zjistí jestli stylesheet modulu existuje pro zadaný vzhled, a podle něj jej vrátí
	 */
	private function selectModuleStylesheetFaceFile($file) {
//		echo AppCore::getTepmlateFaceDir().$this->getModule()->getDir()->getTemplatesDir(false).$file. '<br />';
//		zvolení vzhledu
//		vybraný vzhled
		if(file_exists(AppCore::getTepmlateFaceDir().$this->getModule()->getDir()->getStylesheetsDir(false).$file)){
			$faceFile = AppCore::getTepmlateFaceDir(false).$this->getModule()->getDir()->getStylesheetsDir(false).$file;
		} 
//		Výchozí vzhled
		else if(file_exists(AppCore::getTepmlateDefaultFaceDir().$this->getModule()->getDir()->getStylesheetsDir(false).$file)){
			$faceFile = AppCore::getTepmlateDefaultFaceDir(false).$this->getModule()->getDir()->getStylesheetsDir(false).$file;
		} 
//		Vzhled v engine
		else {
			$faceFile = $this->getModule()->getDir()->getStylesheetsDir().$file;
		};
		
		return $faceFile;
	}
	
	/**
	 * metoda přidává zadaný javascript do výstupu
	 * @param string -- název javascriptu
	 * @param boolean -- true pokud je zadána i cesta se souborem
	 */
	public function addJS($javaScriptName, $withPath = false){
		//TODO kontrola souborů a duplicit
		if(!$withPath){
			if($this->getModule() != null){			
				$javaScriptName = $this->module->getDir()->getJavaScriptsDir().$javaScriptName;
			}
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
	 * Metoda nastaví název modulu, který bude vypsán na začátku
	 * @param string -- název
	 * @param boolena -- (option) jesli se má název přidat za stávající nebo přepsat
	 */
	public function setTplLabel($name, $merge = false) {
		if($this->getModule() != null){
			if($merge){
				$this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_LABEL].=$name;
			} else {
				$this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_LABEL]=$name;
			}
			
		}
	}

	/**
	 * Metoda nastaví podnázev modulu, který bude vypsán na začátku
	 * @param string -- podnázev
	 * @param boolena -- (option) jesli se má název přidat za stávající nebo přepsat
	 * @param string -- (option) oddělovač mezi více nadpisy (default '-')
	 */
	public function setTplSubLabel($name, $merge = false, $separator = '-') {
		if($this->getModule() != null){
			if($merge){
				if(isset($this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_SUBLABEL]) AND $this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_SUBLABEL] != null){
					$separator = ' '.$separator.' ';
				} else {
					$separator = null;
					$this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_SUBLABEL] = null;
				}
				$this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_SUBLABEL].=$separator.$name;
			} else {
				$this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_SUBLABEL]=$name;
			}
			
		}
	}
	
	/**
	 * Metoda nastaví podtitulek modulu, který bude vypsán ve jménu okna
	 * @param string -- podnázev
	 * @param boolena -- (option) jesli se má název přidat za stávající nebo přepsat
	 * @param string -- (option) oddělovač mezi více nadpisy (default '-')
	 */
	public function setSubTitle($name, $merge = false, $separator = '-') {
		if($merge){
				if($this->pageTitle != null){
					$separator = ' '.$separator.' ';
				} else {
					$separator = null;
				}
			
				$this->pageTitle.=$separator.$name;
			} else {
				$this->pageTitle=$name;
			}
	}
	
	/**
	 * Metoda vrací přiřazený název titulku okna
	 * @return string -- titulek okna
	 */
	public function getSubTitle() {
		return $this->pageTitle;
	}
	
	
	/**
	 * Metoda nastaví popis (alt) modulu, který bude vypsán na začátku
	 * @param string -- popis
	 * @param boolena -- (option) jesli se má popis přidat za stávající nebo přepsat
	 */
	public function setTplAlt($name, $merge = false) {
		if($this->getModule() != null){
			if($merge){
				$this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_ALT].=$name;
			} else {
				$this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_ALT]=$name;
			}
			
		}
	}

	/**
	 * Metoda nastaví popis link na kategorii s modulem, který bude vypsán na začátku
	 * (použití asi jenom v panelech)
	 * @param string -- link
	 */
	public function setTplCatLink($link = null) {
		if($link == null){
			$link = new Links();
		}
		if($this->getModule() != null){
			$this->templates[$this->getModule()->getId()][self::TEMPLATE_CATEGORY_LINK]=$link;
		}
	}
		
	/**
	 * statická metoda vrací pole s šablonami modulů (items) kategorií
	 * @return array -- pole s šablonami a proměnými modulů (items)
	 */
//	public static function getCategoryItems() {
//		return self::$items;
//	}
	
	/**
	 * statická metoda vrací pole s proměnými 
	 * @return array -- pole s proměnými
	 */
//	public static function getMainVars() {
//		return self::$tplVars;
//	}
	
	
	/**
	 * Statická metoda vloží proměnou do šablony
	 * @param string -- název proměnné
	 * @param string/array -- hodnota proměnné
	 */
//	public static function addMainVar($varName, $varValue, $arrayName = null){
//		if($arrayName == null){
//			self::$tplVars[$varName] = $varValue;
//		} else {
//			if(!isset(self::$tplVars[$arrayName])){
//				self::$tplVars[$arrayName] = array();
//			}
//			self::$tplVars[$arrayName][$varName] = $varValue;
//		}
//	}
	
	/**
	 * Metoda přiřazuje proměnné do šablony
	 * @param string -- název proměnné
	 * @param string/array -- hodnota proměnné
	 * @param boolean -- true pokud má být proměná zařazena do modulu (default: true)
	 */
	public function addVar($varName, $varValue, $isModuleVar = true) {
		$this->checkVarsArray();
		
		if($isModuleVar AND $this->getModule() != null){
			$this->templates[$this->getModule()->getId()][self::VARIABLES_ARRAY_NAME][$varName] = $varValue;
		} else if($isModuleVar){
			$this->templates[$varName] = $varValue;
		} else {
			$this->engineVars[$varName] = $varValue;
		}
		
	}
	
	/**
	 * Metody zkontroluje vytvoření pole s proměnnými v šabloně
	 */
	private function checkVarsArray() {
//		kontrola hlavního pole
		$this->checkTemplatesArray();
		
		if($this->getModule() != null){
			if(!isset($this->templates[$this->getModule()->getId()][self::VARIABLES_ARRAY_NAME])){
				$this->templates[$this->getModule()->getId()][self::VARIABLES_ARRAY_NAME] = array();
			}
		}
	}
	
	/**
	 * Metoda vrací pole s šablonami a proměnými
	 * @var array -- pole šablon a proměných
	 */
	public function getTemplatesArray() {
		return $this->templates;
	}
	
	/**
	 * Metoda vrací pole s proměnými předanými do enginu
	 * @return array -- pole proměných
	 */
	public function getEngineVarsArray() {
		return $this->engineVars;
	}
	
	
	
	/**
	 * Metoda nastavuje modul
	 * @param Module -- objekt modulu
	 */
	public function setModule(Module $module=null) {
		$this->module = $module;
		
//		echo "<pre>";
//		print_r($this->templates);
//		echo "</pre>";
		$this->checkTemplatesArray();
		
		$this->setTplLabel($this->getModule()->getLabel());
		$this->setTplAlt($this->getModule()->getAlt());
		
//		Přiřazení identifikátoruModulu
		$this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_STYLE_IDENT] = $this->getModule()->getName();
		
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
			if($jsPlugin->isDefaultJsSettingsFile() AND $jsPlugin->getSettingsJsFile() != null){
				$this->addJS($jsPlugin->getJsPluginDir().$jsPlugin->getSettingsJsFile(), true);
			} else if($jsPlugin->getSettingsJsFile() != null) {
				$this->addJS($jsPlugin->getSettingsJsFile());
			}
//			Přidání CSS souboru s nastavením
			if($jsPlugin->isDefaultCssSettingsFile() AND $jsPlugin->getSettingsCssFile() != null){
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