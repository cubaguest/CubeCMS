<?php
/**
 * Vypecky Engine
 *
 * @category   VVE VeproveVypeckyEnginy
 * @package    Main Application file
 * @copyright  Copyright (c) 2008 Jakub Matas
 * @version    $Id: app.php 3.0.0 beta1 29.8.2008
 * @author Jakub Matas <jakubmatas@gmail.com>
 * @abstract Hlavní třída aplikace(Singleton)
 *  
 */

/**
 * Hlavní třída aplikace - singleton
 * Obsluhuje celou aplikaci a její komponenty
 */
class AppCore {
	/**
	 * Výchozí cestak enginu
	 * @var string
	 */
	const MAIN_ENGINE_PATH = './';
	
	/**
	 * Název konfiguračního souboru
	 * @var string
	 */
	const MAIN_CONFIG_FILE = "config.xml";

	/**
	 * Název sekce v konf. souboru s konfigurací databáze
	 * @var string
	 */
	const MAIN_CONFIG_DB_SECTION = "db";

	/**
	 * Konstanta s adresářem s moduly
	 * @var string
	 */
	const MODULES_DIR = "modules";

	/**
	 * Adresář s engine-pluginy
	 * @var string
	 */
	const ENGINE_EPLUINS_DIR = 'EPlugins';

	/**
	 * Adresář s JS-pluginy
	 * @var string
	 */
	const ENGINE_JSPLUINS_DIR = 'JsPlugins';
	
	/**
	 * Adresář s helpery
	 * @var string
	 */
	const ENGINE_HELPERS_DIR = 'helpers';
	
	/**
	 * Adresář s Modely enginu
	 * @var string
	 */
	const ENGINE_MODELS_DIR = 'models';
	
	/**
	 * Kešovací adresář pro dočasné soubory
	 * @var string
	 */
	const ENGINE_CACHE_DIR = 'cache';
	
	/**
	 * Konstanta s adresářem s šablonami systému
	 * @var string
	 */
	const TEMPLATES_DIR = "templates";

	/**
	 * Konstanta s adresářem s obrázky šablony
	 * @var string
	 */
	const TEMPLATES_IMAGES_DIR = "images";

	/**
	 * Konstanta s názvem adresáře se styly
	 * @var string
	 */
	const TEMPLATES_STYLESHEETS_DIR = 'stylesheets';
	
	/**
	 * Konstanta s názvem adresáře se specielními soubory (helpy, atd)
	 * @var string
	 */
	const SPECIALITEMS_DIR = 'specialitems';
	
	/**
	 * prefix názvu sloupců s tabulkami u modulu (dbtable1, dbtable2, atd.)
	 * @var string
	 */
	const MODULE_DBTABLES_PREFIX = "dbtable";

	/**
	 * Sufix pro metody kontroleru
	 * @var string
	 */
	const MODULE_CONTROLLER_SUFIX = 'Controller';
	
	/**
	 * Sufix pro metody viewru
	 * @var string
	 */
	const MODULE_VIEWER_SUFIX = 'View';
	
	/**
	 * Hlavní kontroler modulu - prefix
	 * @var string
	 */
	const MODULE_MAIN_CONTROLLER_PREFIX = 'Main';
	
	/**
	 * Sufix názvu třídy panelů
	 * @var string
	 */
	const MODULE_PANEL_CLASS_SUFIX = 'Panel';
	
	/**
	 * Název kontroleru panelu
	 * @var string
	 */
	const MODULE_PANEL_CONTROLLER = 'panelController';
	
	/**
	 * Název viewru panelu
	 * @var string
	 */
	const MODULE_PANEL_VIEWER = 'panelView';

	/**
	 * Název třídy pro sitemapu -- sufix
	 * @var string
	 */
	const MODULE_SITEMAP_SUFIX_CLASS = 'SiteMap';
	
	/**
	 * Parametr v url, kterým se přenáší typ média pro zobrazení
	 * @var string
	 */
	const MEDIA_URL_PARAM_TYPE = 'media';
	
	/**
	 * Typy médií a jejich hodnota, kterou se předávaji
	 * @var string
	 */
	const MEDIA_PRINT_TYPE = 'print';
	const MEDIA_WWW_TYPE = 'www';
	
	/**
	 * výchozí doména pro gettext
	 * @var string
	 */
	const GETTEXT_DEFAULT_DOMAIN = 'messages';
	
	/**
	 * Gettext engine locales dir
	 * @var string
	 */
	const GETTEXT_DEFAULT_LOCALES_DIR = './locale';
	
	/**
	 * Doména pro gettext a moduly
	 * @var string
	 */
	const GETTEXT_MDOMAIN = 'module_messages';
	
	/**
	 * Konstanta s názvem adresáře se vzhledy
	 * @var string
	 */
	const FACES_DIR = 'faces';
	
	/**
	 * Konstanta obsahuje výchozí název vzhledu (face)
	 * @var string
	 */
	const FACE_DEFAULT_NAME = 'default';

	/**
	 * Instance hlavní třídy
	 * @var AppCoreClass
	 */
	private static $_coreInstance = null;

	/**
	 * Hlavní adresář aplikace
	 * @var string
	 */
	private static $_appWebDir = null;
	
	private static $debugLevel = 1;
	
	/**
	 * Hlavní objekt šablony
	 * @var smarty
	 */
	private $template = null;

	/**
	 * Název vzhledu
	 * @var string
	 */
	private $templateFace = null;
	
	/**
	 * Cesta do zvoleného vzhledu
	 * @var string
	 */
	private static $templateFaceDir = null;

	/**
	 * Cesta do výchozího vzhledu
	 * @var string
	 */
	private static $templateDefaultFaceDir = null;
	
	/**
	 * Objekt pro přístup k šabloně v jádře
	 * @var Template
	 */
	private $coreTpl = null;
	
	/**
	 * Objekt s chybovými hláškami jádra
	 * @var Errors
	 */
	public $coreErrors = null;

	/**
	 * Objekt s chybovými hláškami modulů
	 * @var Errors //TODO
	 */
	public $errors = null;

	/**
	 * Objekt s hláškami modulů
	 * @var Messages 
	 */
	public $messages = null;

	/**
	 * Objekt s chybovými hláškami modulů (NE VYJÍMKY!)
	 * @var Messages
	 */
	public $userErrors = null;

	/**
	 * Objekt Autorizace
	 * @var Auth
	 */
	public $auth = null;

	/**
	 * objekt db konektory
	 * @var DbConnector
	 */
	private static $dbConnector = null;

	/**
	 * objekt právě vybraného modulu
	 * @var Module
	 */
	private static $selectedModule = null;

	/**
	 * objekt konfiguračního souboru
	 * @var Config
	 */
	private static $sysConfig = null;

	/**
	 * Proměná pro sledování doby generování scriptu
	 * @var float
	 */
	private $_stratTime = null;

	/**
	 * Objekt s vybranou kategorií
	 * @var Category
	 */
	public $category = null;

	/**
	 * Typ média na které se bude vypisovat
	 * @var string
	 */
	private static $mediaType = AppCore::MEDIA_WWW_TYPE;
	
	/*
	 * MAGICKÉ METODY
	 */
	
	/**
	 * Konstruktor objektu AppCore
	 */
	private function __construct(){
//		Definice globálních konstant
		define('URL_SEPARATOR', '/');
		
//		inicializace stratovacího času
		List ($usec, $sec) = Explode (' ', microtime());
		$this->_stratTime=((float)$sec + (float)$usec);

		//	nastavení hlavního adresáře aplikace
		$this->setAppMainDir( realpath(dirname(__FILE__)) );

		//	přidání adresáře pro načítání knihoven
		set_include_path('./lib/' . PATH_SEPARATOR . get_include_path());

		//načtení potřebných knihoven
		$this->_loadLibraries();
		
		$this->_initCoreErros();

		$this->_initConfig();
		
//		Zapnutí debugeru
		if(self::sysConfig()->getOptionValue('DEBUG_LEVEL') > 1){
			self::$debugLevel = self::sysConfig()->getOptionValue('DEBUG_LEVEL');
		}

//		Definování konstanty s datovým adresářem modulů //TODO upravit tak aby se to používalo korektně např. proměná
		define('MAIN_DATA_DIR', self::sysConfig()->getOptionValue('data_dir'));
		
//		inicializace sessions
		$this->_initSessions();
		
//		inicializace odkazů
		$this->_initLinks();
		
		//inicializace lokalizace
		$this->_initLocale();

		$this->_initDbConnector();
		
		//		Inicializace chybových hlášek
		$this->_initMessagesAndErrors();
		
		//		Inicializace typu média pro zobrazení
		$this->_initMediaType();

		//Spuštění jádra aplikace
		$this->runApp();


//		Vypsání chyb na výstup
		$this->coreErrors->getErrorsToStdIO();
	}


	/**
	 * Třída je singleton
	 * není povolen clone
	 */
	private function __clone() {
		;
	}

	/*
	 * STATICKÉ METODY
	 */
	
	/**
	 * Singleton instance objektu
	 *
	 * @return AppCore
	 */
    public static function getInstance()
    {
        if (null === self::$_coreInstance) {
            self::$_coreInstance = new self();
        } else {
			throw new Exception("Aplication object have been created");
		}

        return self::$_coreInstance;
    }

	/**
	 * Metoda vrací adresář aplikace
	 * @return string -- adresář aplikace
	 */
	public static function getAppWebDir()
	{
		return self::$_appWebDir;
	}
	
	/**
	 * Metoda vygeneruje instanci aplikace
	 * pokud již instance existuje, bude vyhozena vyjímka
	 * Instance aplikace je singleton
	 *
	 */
	public static function createApp() {
		self::getInstance();
	}
	
	/**
	 * Metoda vrací o jaké médium pro tisk stránky se jedná
	 * @return string
	 */
	public static function media() {
		return AppCore::$mediaType;
	}
	
	/**
	 * Metoda vrací objekt na systémovou konfiguraci
	 * @return Config -- objekt konfigurace
	 */
	public static function sysConfig() {
		return self::$sysConfig;
	}
	
	/**
	 * Metoda vrací adresář ke zvolenému vzhledu
	 * 
	 * @param boolean -- jestli se má vrátit celá cesta nebo jemo část od hlavního adresáře
	 * @return string -- adresář zvoleného vzhledu
	 */
	public static function getTepmlateFaceDir($fullDir = true) {
		if($fullDir){
			return self::$_appWebDir.DIRECTORY_SEPARATOR.self::FACES_DIR.DIRECTORY_SEPARATOR.self::$templateFaceDir.DIRECTORY_SEPARATOR;
		} else {
			return '.'.URL_SEPARATOR.self::FACES_DIR.URL_SEPARATOR.self::$templateFaceDir.URL_SEPARATOR;
		}
	}

	/**
	 * Metoda vrací adresář k výchozímu vzhledu
	 * 
	 * @param boolean -- jestli se má vrátit celá cesta nebo jemo část od hlavního adresáře
	 * @return string -- adresář výchozího vzhledu
	 */
	public static function getTepmlateDefaultFaceDir($fullDir = true) {
		if($fullDir){
			return self::$_appWebDir.DIRECTORY_SEPARATOR.self::FACES_DIR.DIRECTORY_SEPARATOR.self::$templateDefaultFaceDir.DIRECTORY_SEPARATOR;
		} else {
			return '.'.URL_SEPARATOR.self::FACES_DIR.URL_SEPARATOR.self::$templateDefaultFaceDir.URL_SEPARATOR;
		}
	}
	
    /**
     * Metoda vrací objekt db conektoru
     * 
     * @return DbConnector -- objekt db konektoru
     */
	public static function getDbConnector() {
		return self::$dbConnector;
	}
	
    /**
     * Metoda vrací objekt aktuálního modulu
     * 
     * @return Module -- objekt vybraného modulu
     */
	public static function getSelectedModule() {
		return self::$selectedModule;
	}
	
	
	
    /*
     * PRIVÁTNÍ METODY
     */
    
	/**
	 * Metoda pro vytvoření objektu pro obsluhu chyb jádra
	 * (CoreErrors)
	 */
	private function _initCoreErros() {
		$this->coreErrors = new Errors();
		CoreException::_setErrorsHandler($this->coreErrors);
	}
	
	/**
	 * Metoda inicializuje připojení k databázi
	 */
	private function _initDbConnector() {

		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'db' . DIRECTORY_SEPARATOR. 'db.class.php');

		self::$dbConnector = Db::factory(self::sysConfig()->getOptionValue("dbhandler", self::MAIN_CONFIG_DB_SECTION),
										 self::sysConfig()->getOptionValue("dbserver", self::MAIN_CONFIG_DB_SECTION),
										 self::sysConfig()->getOptionValue("dbuser", self::MAIN_CONFIG_DB_SECTION),
										 self::sysConfig()->getOptionValue("dbpasswd", self::MAIN_CONFIG_DB_SECTION),
										 self::sysConfig()->getOptionValue("dbname", self::MAIN_CONFIG_DB_SECTION),
										 self::sysConfig()->getOptionValue("tbprefix", self::MAIN_CONFIG_DB_SECTION));

		if(self::$dbConnector == false){
			new CoreException(self::sysConfig()->getOptionValue("dbhandler", self::MAIN_CONFIG_DB_SECTION)._(" Databázový engine nebyl implementován"), 1);
		}
	}
	
	/**
	 * Metoda inicializuje Seesion
	 * 
	 * //TODO implementovat do třídy Session
	 */
	private function _initSessions() {
//		//Nastaveni session
		Sessions::factory(self::sysConfig()->getOptionValue('session_name'));
	}
	
	
		/**
	 * Metoda inicializuje konfiguraci s konfiguračního souboru
	 *
	 */
	private function _initConfig() {
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'config.class.php');

//		$this->config = new Config(self::getAppWebDir() . DIRECTORY_SEPARATOR . self::MAIN_CONFIG_FILE, $this->coreErrors);
		self::$sysConfig = new Config(self::getAppWebDir() . DIRECTORY_SEPARATOR . self::MAIN_CONFIG_FILE, $this->coreErrors);
	}

	/**
	 * Metoda nastavuje locales a gettext pro překlady
	 */
	private function _initLocale() {
		
		Locale::factory();
//			//Nastaveni jazyku pro generovaní url
////			Links::setLang($selectLang);

		//	nastavení gettext a locales
		putenv("LANG=".Locale::getLocale());
		setlocale(LC_ALL, Locale::getLocale());

		bindtextdomain(self::GETTEXT_DEFAULT_DOMAIN, self::GETTEXT_DEFAULT_LOCALES_DIR);
		textdomain(self::GETTEXT_DEFAULT_DOMAIN);
//		bind_textdomain_codeset("messages", "utf-8");

	}

	/**
	 * Metoda inicializuje třídu pro odkazy
	 */
	private function _initLinks() {
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'links.class.php');
		Links::factory();
	}

	/**
	 * Metoda inicializuje šablonovací systém (SMARTY)
	 */
	private function _initTemplate() {
//		Vložení smarty třídy
		require_once ('.'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'smarty'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'Smarty.class.php');

		$this->template = new Smarty();
		
//		Nastavení proměných smarty
		$this->template->template_dir = self::sysConfig()->getOptionValue("templates_dir", "smarty");
		$this->template->compile_dir = self::sysConfig()->getOptionValue("templates_c_dir", "smarty");
		$this->template->cache_dir = self::sysConfig()->getOptionValue("cache_dir", "smarty");
		$this->template->config_dir = self::sysConfig()->getOptionValue("config_dir", "smarty");

		//nastavení cesty k pluginům smarty
//		$this->template->plugins_dir = array('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'smarty' . DIRECTORY_SEPARATOR . 'libs'
//											 . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR,
//					   						 '.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'smarty' . DIRECTORY_SEPARATOR . 'plugins'
//											 . DIRECTORY_SEPARATOR);
		$this->template->plugins_dir = array(
                       'plugins', // the default under SMARTY_DIR
                       '..'.DIRECTORY_SEPARATOR . 'plugins'.DIRECTORY_SEPARATOR);
		
											 
//		Registrace pluginu
											 
//		Pokud je debug tak vypnout kešování smarty
		if(self::$debugLevel >= 2){
			$this->template->force_compile = true;
			$this->template->clear_compiled_tpl();
		}
		
//		Vytvoření objektu šablon pro jádro
		$this->coreTpl = new Template();

//		inicializace vzhledu
		$this->templateFace = self::sysConfig()->getOptionValue('face');
		self::$templateFaceDir = self::sysConfig()->getOptionValue('face');
		self::$templateDefaultFaceDir = self::FACE_DEFAULT_NAME;
		
//		Přidání do smarty objektu proměných s cestami k adresáři s engine šablonami
		$this->template->template_default_face_dir = self::getTepmlateDefaultFaceDir();
		$this->template->template_default_face_dir_rel = self::getTepmlateDefaultFaceDir(false);
		$this->template->template_face_dir = self::getTepmlateFaceDir();
		$this->template->template_face_dir_rel = self::getTepmlateFaceDir(false);
		$this->template->template_engine_stylesheets_dir = self::TEMPLATES_STYLESHEETS_DIR;
		$this->template->template_engine_images_dir = self::TEMPLATES_IMAGES_DIR;
		$this->template->template_modules_dir = self::MODULES_DIR;
//		$this->template->engine_dir = self::TEMPLATES_STYLESHEETS_DIR;
		
	}
	
	/**
	 * Metoda načte potřebné knihovny
	 */
	private function _loadLibraries() {
		/**
		 * Funkce slouží pro automatické načítání potřebných tříd
		 * @param string -- název třídy
		 */
		function __autoload($className){
			//TODO dodělat kontroly, tak ať to vyhazuje přesnější chbové hlášky
			//		Zmenšení na malá písmena
			$className = strtolower($className);
			//je načítána hlavní knihovna
			if(file_exists('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. $className . '.class.php')){
				require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. $className . '.class.php');
			}
			//je načítán e-plugin
			else if(file_exists('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_EPLUINS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php')) {
				require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_EPLUINS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php');
			}
//			Je-li načítán JsPlugin
			else if(file_exists('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_JSPLUINS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php')) {
				require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_JSPLUINS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php');
			} 
//			Ja-li načítán model
			else if(file_exists('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_MODELS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php')) {
				require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_MODELS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php');
			} 
//			Ja-li načítán helper
			else if(file_exists('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_HELPERS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php')) {
				require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_HELPERS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php');
			} 
//			Je-li načítan model modulu
			else if(AppCore::getSelectedModule() != null AND strpos($className, 'model') !== false){
				$modelFileName = substr($className, 0, strpos($className, 'model'));
				if (file_exists('.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR . AppCore::getSelectedModule()->getName() . DIRECTORY_SEPARATOR. AppCore::ENGINE_MODELS_DIR . DIRECTORY_SEPARATOR . $modelFileName . '.php')){
					require_once ('.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR . AppCore::getSelectedModule()->getName() . DIRECTORY_SEPARATOR. AppCore::ENGINE_MODELS_DIR . DIRECTORY_SEPARATOR . $modelFileName . '.php');
				}
			}
			else {
				new CoreException(_("Nepodařilo se načíst potřebnou systémovou třídu ").$className, 2);
			}
		}
		
//		Abstraktní třída pro JsPluginy
//		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'JsPlugins'. DIRECTORY_SEPARATOR . 'jsplugin.calss.php');
		
		
//		knihovny pro práci s chybami
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'Exceptions' . DIRECTORY_SEPARATOR . 'coreException.class.php');
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'Exceptions' . DIRECTORY_SEPARATOR . 'errors.class.php');
	}
	
	/**
	 * Metoda inicializuje objekty pro práci s hláškami
	 *
	 */
	private function _initMessagesAndErrors()
	{
		//		Vytvoření objektu pro práci se zprávami
		$this->messages = new Messages('session', 'mmessages', true);
		$this->userErrors = new Messages('session', 'merror');
	}
	
	/**
	 * Metoda inicializuje typ média pro zobrazení
	 */
	private function _initMediaType() {
		//TODO dodělat navázání na knihovnu Links, tak aby nebyla závislá na $_GET
		if(isset($_GET[self::MEDIA_URL_PARAM_TYPE])){
			AppCore::$mediaType = $_GET[self::MEDIA_URL_PARAM_TYPE];
		}
	}
	
//	Metoda inicializuje moduly
	private function _initModules() {
		//		Načtení potřebných knihoven
		$this->loadModuleLibs();
		
		ModuleDirs::setWebDir(self::MAIN_ENGINE_PATH); //TODO patří přepsat tak aby se to zadávalo jinde
		ModuleDirs::setWebDataDir(self::sysConfig()->getOptionValue("data_dir"));;
	}
	
	
	
	/**
	 * Metoda ověřuje autorizaci přístupu
	 */
	private function coreAuth() {
		$this->auth = new Auth(self::$dbConnector, $this->userErrors);
	}
	
	/**
	 * Metoda načte potřebné knihovny pro moduly
	 */
	private function loadModuleLibs() {
//		načtení hlavních tříd modulu (controler, view)
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'controller.class.php');
//		třída pro práci s pohledem
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'view.class.php');
	}
	
	/**
	 * Metoda přiřadí popisky enginu do hlavní šablony
	 */
	private function assignEngineLabelsToTpl()
	{
//		Popisky loginu
		$this->assignVarToTpl("LOGIN_LOGOUT_BUTTON_NAME", _("Odhlásit"));
		$this->assignVarToTpl("LOGIN_LOGIN_BUTTON_NAME", _("Přihlásit"));
	}
	
	/**
	 * Metoda přiřadí proměné z šablony do hlavní šablony
	 * @param Template -- Objekt šablonovacího systému
	 * @param string -- název v hlavní šabloně 
	 */
	private function assginTplObjToTpl(Template $templateObject, $templateArrayName = null) {
		if($templateArrayName != null){
			$this->assignVarToTpl($templateArrayName, $templateObject->getTemplatesArray());
		} else {
			foreach ($templateObject->getTemplatesArray() as $key => $value) {
				$this->assignVarToTpl($key, $value);
			}	
		}
		$this->assignVarToTpl('MAIN_MODULE_TITLE', $templateObject->getSubTitle());
		
		$engineVars = $templateObject->getEngineVarsArray();
		if(!empty($engineVars)){
			$this->assignVarToTpl('MODULE_ENGINE_VARS', $engineVars);
		}
	}
	
	/**
	 * Metoda přiřadí proměnou do šablony
	 * @param string -- název proměné v šabloně
	 * @param mixed -- hodnota
	 */
	private function assignVarToTpl($varName, $value) {
//		$this->template->assign($varName, $value);
		$merge = false;
		
		if($this->template->get_template_vars($varName) != null){
			$this->template->append($varName, $value, null, true);
		} else {
			$this->template->assign($varName, $value);
		}
	}
	
	
	
	/*
	 * VEŘEJÉ METODY
	 */
    
	/**
	 * Metoda nastavuje hlavní adresář aplikace
	 *
	 * @param string -- hlavní adresář aplikace
	 */
	public function setAppMainDir($appMainDir) {
		self::$_appWebDir = $appMainDir;
	}

	/**
	 * Metoda vytvoří hlavní menu aplikace
	 */
	public function createMainMenu() {
		if(file_exists('.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR. 'menu.class.php')){
			require_once ('.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR. 'menu.class.php');
		} else {
			new CoreException(_("Nebyl nalezen soubor pro vytvoření hlavního menu"), 1);
		}
		
		if(class_exists("Menu")){
			$menu = new Menu(self::$dbConnector, $this->auth);
			
			$menu->controller();
			
			$menu->view();
			
			$this->assginTplObjToTpl($menu->getTemplate(), MainMenu::TPL_ARRAY_NAME);
			
		} else {
			new CoreException(_("Třída s hlavním menu neexistuje"), 1);
		}
		
//		Přiřazení souboru s šablonou menu podle zvoleéhoí vzhledu
//		vybraný vzhled šablony //TODO přesunout do třídy pro práci s menu
		if(file_exists(self::getTepmlateFaceDir().self::TEMPLATES_DIR.DIRECTORY_SEPARATOR.'menu.tpl')){
			$faceMenuFile = '.'.self::getTepmlateFaceDir(false).self::TEMPLATES_DIR.URL_SEPARATOR.'menu.tpl';
		} 
//		Výchozí vzhled
		else if(file_exists(self::getTepmlateDefaultFaceDir().self::TEMPLATES_DIR.DIRECTORY_SEPARATOR.'menu.tpl')){
			$faceMenuFile = self::getTepmlateDefaultFaceDir().self::TEMPLATES_DIR.DIRECTORY_SEPARATOR.'menu.tpl';
		} 
//		Vzhled v engine
		else {
			$faceMenuFile = 'menu.tpl';
		}
		
		$this->assignVarToTpl('MAIN_MENU_TEMPLATE_FILE', $faceMenuFile);
	}

	/**
	 * Metoda nastaví překlad na překlad enginu
	 */
	private function setToEnginetranslator() {
		;
	}
	
	
	/**
	 * Metoda přiřadí do šablony hlavní proměnné systému
	 */
	public function assignMainVarsToTemplate() {
		//	Hlavni promene strany
//		$this->template->assign("MAIN_PAGE_TITLE", self::sysConfig()->getOptionValue("web_name"));
//		Template::addMainVar("MAIN_PAGE_TITLE", self::sysConfig()->getOptionValue("web_name"));
		$this->coreTpl->addVar("MAIN_PAGE_TITLE", self::sysConfig()->getOptionValue("web_name"));
		
		//adresa k rootu webu
//		$this->template->assign("MAIN_ROOT_DIR", self::getAppWebDir());
		$this->coreTpl->addVar("MAIN_ROOT_DIR", self::getAppWebDir());

		$link = new Links();
//		$link->getMainWebDir();
//		$this->template->assign("MAIN_WEB_DIR", $link->getMainWebDir());
		$this->coreTpl->addVar("MAIN_WEB_DIR", $link->getMainWebDir());
		$this->coreTpl->addVar("THIS_PAGE_LINK", $link);
		
//		cesty k obrázkům enginu
//		echo "<pre>";
//		print_r($this->template);
//		echo "</pre>";
		
//		$this->coreTpl->addVar("MAIN_IMAGES_PATH", self::sysConfig()->getOptionValue('images', 'dirs'));
		$this->coreTpl->addVar("MAIN_LANG_IMAGES_PATH", self::sysConfig()->getOptionValue('images_lang', 'dirs').URL_SEPARATOR);
		$this->coreTpl->addVar("MAIN_CURRENT_FACE_PATH", self::getTepmlateFaceDir(false));
		$this->coreTpl->addVar("MAIN_CURRENT_FACE_IMAGES_PATH", self::getTepmlateFaceDir(false).self::sysConfig()->getOptionValue('images', 'dirs').URL_SEPARATOR);
		unset($link);
		
//		$mainTpl->assign("MAIN_ROOT_DIR", $links->getMainWebDir());

		//Přihlášení uživatele
		$this->coreTpl->addVar("USER_IS_LOGIN", $this->auth->isLogin());
		$this->coreTpl->addVar("NOT_LOGIN_USER_NAME",_("Nepřihlášen"));
		$this->coreTpl->addVar("LOGIN_USER_NAME",_("Přihlášen"));
		$this->coreTpl->addVar("USER_LOGIN_USERNAME", $this->auth->getUserName());
		
		//Verze enginu
		$this->coreTpl->addVar("ENGINE_VERSION", self::sysConfig()->getOptionValue("engine_version"));

		//Debugovaci mod
		if (self::$debugLevel > 1){
			$this->coreTpl->addVar("DEBUG_MODE", true);
		}
		
//		Přiřazení jazykového pole
		$this->coreTpl->addVar("APP_LANGS_NAMES", Locale::getAppLangsNames());		
		
//		Vytvoření odkazů s jazyky
		$langs = array();
		$langNames = Locale::getAppLangsNames();
		$link = new Links();
		foreach (Locale::getAppLangs() as $langKey => $lang) {
			$langArr = array();
			$langArr['name'] = $lang;
			$langArr['label'] = $langNames[$lang];
			if($lang != Locale::getDefaultLang()){
				$langArr['link'] = (string)$link->lang($lang);
			} else {
				$langArr['link'] = (string)$link->lang();
			}
			array_push($langs, $langArr);
		}
		unset($langNames);
		unset($link);
		unset($langArr);
		
		$this->coreTpl->addVar('APP_LANGS' ,$langs);
		unset($langs);
	}

	/**
	 * metoda vyrenderuje šablonu
	 */
	public function renderTemplate() {
//		načtení doby zpracovávání aplikace
		List ($usec, $sec) = Explode (' ', microtime());
		$endTime = ((float)$sec + (float)$usec);
//		$this->template->assign("MAIN_EXEC_TIME", round($endTime-$this->_stratTime, 4));
		$this->coreTpl->addVar("MAIN_EXEC_TIME", round($endTime-$this->_stratTime, 4));
//		$this->template->assign("COUNT_ALL_SQL_QUERY", $mysql->getCountAllSqlQuery());
		$this->coreTpl->addVar("COUNT_ALL_SQL_QUERY", Db::getCountQueries());
		
		
//		Přiřazení popisků do šablony
		$this->assignEngineLabelsToTpl();

		//		Přiřazení javascriptů a stylů
		$this->assignVarToTpl("STYLESHEETS", Template::getStylesheets());
		$this->assignVarToTpl("JAVASCRIPTS", Template::getJavaScripts());
		
//		Přiřazení proměných z hlavní šablony
		$this->assginTplObjToTpl($this->coreTpl);
		
//		zvolení vzhledu
//		vybraný vzhled šablony
		if(file_exists(self::getTepmlateFaceDir().self::TEMPLATES_DIR.DIRECTORY_SEPARATOR.'index.tpl')){
			$faceFilePath = self::getTepmlateFaceDir().self::TEMPLATES_DIR.DIRECTORY_SEPARATOR;
		} 
//		Výchozí vzhled
		else if(file_exists(self::getTepmlateDefaultFaceDir().self::TEMPLATES_DIR.DIRECTORY_SEPARATOR.'index.tpl')){
			$faceFilePath = self::getTepmlateDefaultFaceDir().self::TEMPLATES_DIR.DIRECTORY_SEPARATOR;
		} 
//		Vzhled v engine
		else {
			$faceFilePath = '';
		}
		
//		Zvolení zobrazovaného média
		switch (AppCore::media()) {
			case AppCore::MEDIA_PRINT_TYPE:
				$this->template->display($faceFilePath."index-print.tpl");
			break;
			
			default:
				$this->template->display($faceFilePath."index.tpl");
			break;
		}

//		Při debug levelu vyšším jak 3 zobrazit výpis šablony
		if(self::$debugLevel >= 3){
			echo "<pre style='text-align: left'>";
			print_r($this->template->get_template_vars());
			echo "</pre>";
		}
	}

	/**
	 * Metoda vytvoří pole tabulek modulu
	 * 
	 * @param SqlObject -- objekt s tabulkami
	 * @return array -- pole s tabulkama
	 */
	private function getModuleTables($item) {
		$tableIndex = 1; $moduleDbTables = array();
//		TODO potřebuje optimalizaci a OPRAVIT
		$objectName=self::MODULE_DBTABLES_PREFIX.$tableIndex;
		while (isset($item->$objectName) AND ($item->$objectName != null)){
			$moduleDbTables[$tableIndex] = $item->$objectName;
			$tableIndex++;
			$objectName=self::MODULE_DBTABLES_PREFIX.$tableIndex;
		};
		
		return $moduleDbTables;
	}
	
	
	/**
	 * Metoda spouští moduly
	 * //TODO
	 */
	public function runModules() {

//načtení dat z db
		$itemsTable = self::sysConfig()->getOptionValue("items_table", Config::SECTION_DB_TABLES);
		$modulesTable = self::sysConfig()->getOptionValue("modules_table", Config::SECTION_DB_TABLES);
//		$userNameGroup = $this->auth->userdetail->offsetGet(Auth::USER_GROUP_NAME);
		$userNameGroup = $this->auth->getGroupName();

//		Vytvoření dotazu pro db
		$catItemsSelect = self::$dbConnector->select()
						   ->from(array("item" => $itemsTable))
						   ->join(array("module" => $modulesTable), "item.id_module=module.id_module", null)
						   ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup." LIKE \"r__\"")
						   ->where("item.id_category = '".Category::getId()."'", "and")
						   ->order("item.priority", "desc")
						   ->order("item.label");


		$items = self::$dbConnector->fetchObjectArray($catItemsSelect);

		if($items != null){
//			Vytvoření objektu s článkem (article) //TODO pouze pro jeden článek
			$article = new Article();

			foreach ($items as $itemIndex => $item) {
				//			Vytvoření objektu šablony
				$template = new Template();
				
//				Příprava pro objekt modulu
//				Načtení všech tabulek modulu
//				$tableIndex = 1; $moduleDbTables = array();
////				//TODO potřebuje optimalizaci a OPRAVIT
//				$objectName=self::MODULE_DBTABLES_PREFIX.$tableIndex;
//				while (isset($item->$objectName) AND ($item->$objectName != null)){
//					$moduleDbTables[$tableIndex] = $item->$objectName;
//					$tableIndex++;
//					$objectName=self::MODULE_DBTABLES_PREFIX.$tableIndex;
//				}

//				Vytvoření objektu pro práci s modulem
//				$module = new Module($item, $moduleDbTables);
				$module = new Module($item, $this->getModuleTables($item));
//				klonování modulu do statické proměné enginu
				self::$selectedModule = clone $module;
				
//				vytvoření pole se skupinama a právama
				$userRights = array();
				foreach ($item as $collum => $value){
					if (substr($collum,0,strlen(Rights::RIGHTS_GROUPS_TABLE_PREFIX)) == Rights::RIGHTS_GROUPS_TABLE_PREFIX){
						$userRights[substr($collum,strlen(Rights::RIGHTS_GROUPS_TABLE_PREFIX), strlen($collum))]=$value;
					}
				}
//				Vytvoření objektu pro přístup k právům modulu
				$moduleRights = new Rights($this->auth, $userRights);
				
				
//				načtení souboru s akcemi modulu
				if(file_exists('.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'action.class.php')){
					require '.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'action.class.php';
				} else {
					new CoreException(_("Nepodařilo se nahrát akci modulu ") . $module->getName(), 12);
				}
				
				$action = null;
				if(class_exists("ModuleAction")){
					$action = new ModuleAction($module, $article);
				}
				
//				načtení souboru s cestami (routes) modulu
				if(file_exists('.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'routes.class.php')){
					require '.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'routes.class.php';
				} else {
					new CoreException(_("Nepodařilo se nahrát cestu modul ") . $module->getName(), 10);
				}
				
				$routes = null;
				if(class_exists("ModuleRoutes")){
					$routes = new ModuleRoutes($article);
				}
				
//				načtení souboru s kontrolerem modulu
				if(file_exists('.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'controler.class.php')){
					require '.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'controler.class.php';
				} else {
					new CoreException(_("Nepodařilo se nahrát controler modulu ") . $module->getName(), 5);
				}

				//			Vytvoření objektu kontroleru
				$controllerClassName = ucfirst($module->getName()).'Controller';
				if(class_exists($controllerClassName)){
//					Příprava a nastavení použití překladu
					
					$controller = new $controllerClassName($module, $action, $moduleRights, $this->messages, $this->userErrors, $article);

					//TODO není kompletí typ akce
					$actionCtrl = $action->getAction();
					
					/*
					 * Pokud je zadán jenom článek a žádná akce
					 * je použita výchozí akce (show)
					 */
					if(!$action->isAction() AND $article->isArticle()){
						$actionCtrl = $action->getDefaultAction();
					}

//					Vygenerování typu použitého kontroleru
					$actionCtrl == null ? $actionCtrl=strtolower(self::MODULE_MAIN_CONTROLLER_PREFIX) : null;
					$routes->getRoute() != null ? $actionCtrl = ucfirst($actionCtrl) : null;
					$controllerAction = $routes->getRoute().$actionCtrl.self::MODULE_CONTROLLER_SUFIX;
					$viewAction = $routes->getRoute().$actionCtrl.self::MODULE_VIEWER_SUFIX;

//					Nastevní viewru v kontroleru
					$controller->setView($viewAction);
					
//					Příprava a nastavení použití překladu
					Locale::bindTextDomain($module->getName());
					
//					Zvolení překladu na modul
					Locale::switchToModuleTexts($module->getName()); 
//					zvolení akce kontroleru
					$defaultAction = false;
					
					if($action->haveAction() AND method_exists($controller, $controllerAction)){//TODO doladit	
//					if(method_exists($controller, $controllerAction)){
						$controller->$controllerAction();
					} 
					else if(!$action->isAction() AND $article->isArticle() AND method_exists($controller, $controllerAction)){ 
						$controller->$controllerAction();
					} 
					else {
						$controller->mainController();
						$defaultAction = true;
						if(!method_exists($controller, $controllerAction)){
							//	Vrácení překladu na engine
							Locale::switchToEngineTexts();
							new CoreException(_("Action Controller ").$controllerAction._(" v modulu ") . $module->getName()._(" nebyl nalezen"), 11);
						}
					}
					
					//			načtení souboru s viewrem modulu
					if(file_exists('.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'view.class.php')){
						require '.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'view.class.php';
					} else {
						Locale::switchToEngineTexts();
						new CoreException(_("Nepodařilo se nahrát viewer modulu ") . $module->getName(), 7);
					}
					
//					Donastavení šablon
					$template->setModule($module);
					
//					Spuštění viewru
					$controller->runView($template);
					
//					Uložení šablona a proměných do hlavní šablony
					$this->assginTplObjToTpl($template, 'MODULES_TEMPLATES');
					
					unset($template);

					
					//	Vrácení překladu na engine
					Locale::switchToEngineTexts();
					unset($controller);
					$isModuleControlerRun = true;
				} else {
					new CoreException(_("Nepodařilo se vytvořit objekt controleru modulu ") . $module->getName(), 6);
				}
				
//				Vrácení překladu na engine pro jistotu
				Locale::switchToEngineTexts();

//				přepnutí překladu na engine
				textdomain(self::GETTEXT_DEFAULT_DOMAIN); 

//				odstranění proměných
				unset($module);
				self::$selectedModule = null;
				unset($model);
				unset($action);
				unset($routes);
				unset($controller);
			}
		} else {
			if(new Links() == new Links(true)){
				new CoreException(_("Nepodařilo se nahrát prvky kategorie z databáze."), 9);
			} else {
				$redir = new Links(true);
				$redir->category()->action()->article()->params()->reload();
			}
		}
	}
	
	/**
	 * Metoda inicializuje a spustí levý panel
	 */
	public function runPanel($side){

		switch ($side) {
			case 'right':
				$panelSideUpper = strtoupper($side);
				$panelSideLower = strtolower($side);
			break;
			
			default:
				$side = 'left';
				$panelSideUpper = strtoupper($side);
				$panelSideLower = strtolower($side);
			break;
		}
				
//		Zapnutí panelu
		$this->assignVarToTpl($panelSideUpper."_PANEL", true);
		
		$panelsTable = self::sysConfig()->getOptionValue("panels_table", "db_tables");
		$modulesTable = self::sysConfig()->getOptionValue("modules_table", "db_tables");
		$itemsTable = self::sysConfig()->getOptionValue("items_table", "db_tables");
		$categoryTable = self::sysConfig()->getOptionValue("category_table", "db_tables");
		
//		Načtení panelů u db
		$sqlSCelect = self::getDbConnector()->select()
						   ->from(array("panel" => $panelsTable), "label")
						   ->join(array("items" => $itemsTable), "items.id_item=panel.id_item", null)
						   ->join(array("cat" => $categoryTable), "cat.id_category=items.id_category", null, "urlkey")
						   ->join(array("module" => $modulesTable), "items.id_module=module.id_module", null)
						   ->where("items.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$this->auth->getGroupName()." LIKE \"r__\"")
						   ->where("panel.position = '".$panelSideLower."'", "and")
						   ->where("panel.enable = ".(int)true, "and")
						   ->order("panel.priority", "desc");
						   
		$panelData = self::getDbConnector()->fetchObjectArray($sqlSCelect);
		
//		echo "<pre>";
//		print_r($panelData);
//		echo "</pre>";
		
		if(!empty($panelData)){
			$panelTemplate = new Template();
			
			foreach ($panelData as $panel) {
				$tableIndex = 1; $moduleDbTables = array();
				$objectName=self::MODULE_DBTABLES_PREFIX.$tableIndex;
				while (isset($panel->$objectName) AND ($panel->$objectName != null)){
					$moduleDbTables[$tableIndex] = $panel->$objectName;
					$tableIndex++;
					$objectName=self::MODULE_DBTABLES_PREFIX.$tableIndex;
				}
				//			while (array_key_exists(self::MODULE_DBTABLES_PREFIX.$tableIndex, $item) AND ($item[self::MODULE_DBTABLES_PREFIX.$tableIndex] != null)) {
				$panelModule = new Module($panel, $moduleDbTables);
				self::$selectedModule = clone $panelModule;	
				
				if(file_exists('.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $panelModule->getName() . DIRECTORY_SEPARATOR . 'panel.class.php')){
					include '.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $panelModule->getName() . DIRECTORY_SEPARATOR . 'panel.class.php';
				} else {
					new CoreException(_("Controler a Viewer panelu ").$panelModule->getName()._(" neexistuje."),15);
				}
				
				//				vytvoření pole se skupinama a právama
				$userRights = array();
				foreach ($panel as $collum => $value){
					if (substr($collum,0,strlen(Rights::RIGHTS_GROUPS_TABLE_PREFIX)) == Rights::RIGHTS_GROUPS_TABLE_PREFIX){
						$userRights[substr($collum,strlen(Rights::RIGHTS_GROUPS_TABLE_PREFIX), strlen($collum))]=$value;
					}
				}
//				Vytvoření objektu pro přístup k právům modulu
				$panelRights = new Rights($this->auth, $userRights);
				
				$panelClassName = ucfirst($panelModule->getName()).self::MODULE_PANEL_CLASS_SUFIX;
				
				
				//				Příprava a nastavení použití překladu
//				Locale::switchToModuleTexts();
//				bindtextdomain($panelModule->getName(), '.'.DIRECTORY_SEPARATOR.self::MODULES_DIR.DIRECTORY_SEPARATOR.$panelModule->getName().DIRECTORY_SEPARATOR."locale");
				
				
				if(class_exists($panelClassName)){
//					Nastavení kategorie
					$category[Category::COLUM_CAT_URLKEY] = $panel->{Category::COLUM_CAT_URLKEY};
//					$category[Category::COLUM_CAT_ID] = $panel->{Category::COLUM_CAT_ID}; //TODO nejsou načítány z DB
//					$category[Category::COLUM_CAT_LABEL] = $panel->{Category::COLUM_CAT_LABEL};				

//					nastavení tempalte
					$panelTemplate->setModule($panelModule);
					$link = new Links(true);
					$panelTemplate->setTplCatLink($link->category($category[Category::COLUM_CAT_URLKEY]));
					


					$panel = new $panelClassName($category, $this->messages, $this->userErrors, $panelTemplate, $panelRights);

//					spuštění controleru
					if(method_exists($panel, self::MODULE_PANEL_CONTROLLER)){
						Locale::switchToModuleTexts(); //jazykové nastavení na modul
						$panel->{self::MODULE_PANEL_CONTROLLER}();
						Locale::switchToEngineTexts(); //jazykové nastavení na engine
					} else {
						new CoreException(_("Neexistuje controler panelu ").$panelModule->getName().".",17);
					}

//					spuštění viewru
					if(method_exists($panel, self::MODULE_PANEL_VIEWER)){
						Locale::switchToModuleTexts(); //jazykové nastavení na modul
						$panel->{self::MODULE_PANEL_VIEWER}();
						Locale::switchToEngineTexts(); //jazykové nastavení na engine
					} else {
						new CoreException(_("Neexistuje viewer panelu ").$panelModule->getName().".",18);
					}

				} else {
					new CoreException(_("Třídat ").$panelClassName._(" panelu ").$panelModule->getName()._(" neexistuje."),16);
				}


				
			}
		}
//		Přiřazení panelů do šablony
		if(isset($panelTemplate)){
			$this->assginTplObjToTpl($panelTemplate, 'PANEL_'.$panelSideUpper.'_TEMPLATES');
		}
		unset($panelTemplate);				   
	}

	/**
	 * metoda vybere, která kategorie je vybrána a uloží je di objektu kategorie
	 */
	public function selectCategory() {
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'category.class.php');

//		$this->category = new Category($this->dbConnector, self::sysConfig(), $this->auth);
		Category::factory(self::$dbConnector, $this->auth);
		
		
//		$this->assignVarToTpl("MAIN_CATEGORY_TITLE", $this->category->getLabel());
		$this->assignVarToTpl("MAIN_CATEGORY_TITLE", Category::getLabel());
		
	}

	public function assignCoreErrorsToTpl() {
		$this->assignVarToTpl("CORE_ERRORS", CoreException::getAllExceptions());
		$this->assignVarToTpl("CORE_ERRORS_EMPTY", CoreException::isEmpty());

//		$coreExceptions = CoreException::getAllExceptions();
	}

	/**
	 * Metoda přiřadí všechny proměnné do šablonovacího systému
	 * //TODO není omplementována, vytvořit načítání do šablony.
	 */
	public function assignModuleVarsToTpl() {
		$this->assignVarToTpl("MESSAGES", $this->messages->getMessages());
		$this->assignVarToTpl("ERRORS", $this->userErrors->getMessages());		
		
//		echo "<pre>";
//		print_r($this->messages->getMessages());
//		echo "</pre>";

	}
	
	/**
	 * Metoda vytváří sitemapu a odesílá ji na výstup
	 *
	 */
	public function runSitemap() {
		//načtení dat z db
		$itemsTable = self::sysConfig()->getOptionValue("items_table", Config::SECTION_DB_TABLES);
		$modulesTable = self::sysConfig()->getOptionValue("modules_table", Config::SECTION_DB_TABLES);
		$categoryTable = self::sysConfig()->getOptionValue("category_table", Config::SECTION_DB_TABLES);
//		$userNameGroup = $this->auth->userdetail->offsetGet(Auth::USER_GROUP_NAME);
		$userNameGroup = $this->auth->getGroupName();

//		Vytvoření dotazu pro db
//SELECT cat.*, module.* FROM `vypecky_categories` AS cat
//JOIN vypecky_items AS item ON cat.id_category=item.id_category
//LEFT JOIN vypecky_modules AS module ON item.id_module = module.id_module
//WHERE item.group_guest = 'r--'
//GROUP BY item.id_category
//ORDER BY item.priority DESC, cat.priority DESC
		define('SITEMAP_COLUMN_CHANGEFREQ', 'sitemap_changefreq');
		define('SITEMAP_COLUMN_PRIORITY', 'sitemap_priority');
		
		$itemsSelect = self::$dbConnector->select()
						   ->from(array("cat" => $categoryTable))
						   ->join(array("item" => $itemsTable), "item.id_category=cat.id_category", null)
						   ->join(array("module" => $modulesTable), "module.id_module=item.id_module", 'LEFT')
						   ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup." LIKE \"r__\"")
						   ->group('item.id_category')
						   ->order("cat.priority", "desc")
						   ->order("item.priority", 'desc');


		$items = self::$dbConnector->fetchObjectArray($itemsSelect);

		
		
		if($items != null){
			foreach ($items as $itemIndex => $item) {
//				Vytvoření objektu pro práci s modulem
				$module = new Module($item, $this->getModuleTables($item));
//				klonování modulu do statické proměné enginu
				self::$selectedModule = clone $module;

				$link = new Links(true);
				$link = $link->category($item->{Category::COLUM_CAT_URLKEY});
				
				$moduleName = ucfirst($module->getName());
				$moduleClass = $moduleName.self::MODULE_SITEMAP_SUFIX_CLASS;
//				Pokud existuje soubor tak jej načteme
				if(file_exists($module->getDir()->getMainDir().strtolower(self::MODULE_SITEMAP_SUFIX_CLASS).'.class.php')){
					include ($module->getDir()->getMainDir().strtolower(self::MODULE_SITEMAP_SUFIX_CLASS).'.class.php');
					if(class_exists($moduleClass)){
						$sitemap = new $moduleClass($module, $link, $item->{SITEMAP_COLUMN_CHANGEFREQ}, $item->{SITEMAP_COLUMN_PRIORITY});
					}
				}
//				jinak se vytvoří výchozí
				else {
					$sitemap = new SiteMap($module, $link, $item->{SITEMAP_COLUMN_CHANGEFREQ}, $item->{SITEMAP_COLUMN_PRIORITY});
				}
				
				$sitemap->run();
						
				unset($sitemap);
				
			}
//			Vygenerování mapy
//			Podle vyhledávače
			$searchEngine = htmlspecialchars($_GET['for']);
			echo SiteMap::generateMap($searchEngine);
		}
		
	}
	
	

	/**
	 * Hlavní metoda provádění aplikace
	 */
	public function runApp() {
		//autorizace přístupu
		$this->coreAuth();

//		Inicializace modulu
		$this->_initModules();
		
		switch (self::media()) {
			case 'sitemap':
				$this->runSitemap();
			break;
			
			default:
//				Výchozí je zobrazena stránka
//				Příprava šablony

//				inicializace šablonovacího systému
				$this->_initTemplate();
				
				//nastavení vybrané kategorie
				$this->selectCategory();

				//vytvoření hlavního menu
				$this->createMainMenu();

				//		spuštění modulů
				$this->runModules();

				//		spuštění panelů
				//		Levý
				if(Category::isLeftPanel()){
					$this->runPanel('left');
				}
				//		Pravý
				if(Category::isRightPanel()){
					$this->runPanel('right');
				}

				//		Přiřazení proměných modulů do šablony
				$this->assignModuleVarsToTpl();

				//		přiřazení hlavních proměných
				$this->assignMainVarsToTemplate();

				//		přiřazení chbových hlášek do šablony
				$this->assignCoreErrorsToTpl();

				//		render šablony
				$this->renderTemplate();
			break;
		}
	}
}

?>