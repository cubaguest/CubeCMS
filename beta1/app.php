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
	 * Zapnutí/vypnutí debug módu
	 * @var boolean
	 */
	//const DEBUG = true;

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
	 * Konstanta s adresářem s šablonami systému
	 * @var string
	 */
	const TEMPLATES_DIR = "templates";

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
	public $dbConnector = null;

	/**
	 * objekt konfiguračního souboru
	 * @var Config
	 */
	public $config = null;

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
		if($this->config->getOptionValue('DEBUG_LEVEL') > 1){
			self::$debugLevel = $this->config->getOptionValue('DEBUG_LEVEL');
		}

//		Definování konstanty s datovým adresářem modulů //TODO upravit tak aby se to používalo korektně např. proměná
		define('MAIN_DATA_DIR', $this->config->getOptionValue('data_dir'));
		
//		inicializace sessions
		$this->_initSessions();
		
//		inicializace odkazů
		$this->_initLinks();
		
		//inicializace lokalizace
		$this->_initLocale();

		$this->_initDbConnector();
		
		$this->_initMessagesAndErrors();

//		Inicializace typu média pro zobrazení
		$this->_initMediaType();
		
//		inicializace šablonovacího systému
		$this->_initTemplate();

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

		$this->dbConnector = Db::factory($this->config->getOptionValue("dbhandler", self::MAIN_CONFIG_DB_SECTION),
										 $this->config->getOptionValue("dbserver", self::MAIN_CONFIG_DB_SECTION),
										 $this->config->getOptionValue("dbuser", self::MAIN_CONFIG_DB_SECTION),
										 $this->config->getOptionValue("dbpasswd", self::MAIN_CONFIG_DB_SECTION),
										 $this->config->getOptionValue("dbname", self::MAIN_CONFIG_DB_SECTION),
										 $this->config->getOptionValue("tbprefix", self::MAIN_CONFIG_DB_SECTION));

		if($this->dbConnector == false){
//			new CoreException($this->config->getOptionValue("dbhandler", self::MAIN_CONFIG_DB_SECTION)._(" Database engine not implemented"), 1);
			new CoreException($this->config->getOptionValue("dbhandler", self::MAIN_CONFIG_DB_SECTION)._(" Databázový engine nebyl implementován"), 1);
		}
	}
	
	/**
	 * Metoda inicializuje Seesion
	 */
	private function _initSessions() {
		//Nastaveni session
		session_regenerate_id(); // ochrana před Session Fixation
		// 	Nastaveni limutu pro automaticke odhlaseni
		/* set the cache limiter to 'private' */

		session_cache_limiter('private');
		$cache_limiter = session_cache_limiter();

		/* set the cache expire to 30 minutes */
		session_cache_expire(30);
		$cache_expire = session_cache_expire();

		//session_set_cookie_params(1800);
		session_name($this->config->getOptionValue("session_name"));
		session_start();;
	}
	
	
		/**
	 * Metoda inicializuje konfiguraci s konfiguračního souboru
	 *
	 */
	private function _initConfig() {
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'config.class.php');

		$this->config = new Config(self::getAppWebDir() . DIRECTORY_SEPARATOR . self::MAIN_CONFIG_FILE,
								   $this->coreErrors);
	}

	/**
	 * Metoda nastavuje locales a gettext pro překlady
	 */
	private function _initLocale() {
		
		Locale::factory($this->config);
		

//		if (!isset($_GET["lang"])){
////			include './lang/'.DEFAULT_LANG.'.php';
////			$selectLang=Locale::getLang();
//			//	Links::setLang(null);
//		} else {
////			if (file_exists("./lang/".$_GET["lang"].".php")){
////				include './lang/'.$_GET["lang"].'.php';
////			} else {
////				include './lang/'.DEFAULT_LANG.'.php';
////				$errorEngine->addMessage($errMsg[101], 101);
////			}
//			
//			$selectLang=$_GET["lang"];
//			//Nastaveni jazyku pro generovaní url
////			Links::setLang($selectLang);
//		}

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
		$this->template->template_dir = $this->config->getOptionValue("templates_dir", "smarty");
		$this->template->compile_dir = $this->config->getOptionValue("templates_c_dir", "smarty");
		$this->template->cache_dir = $this->config->getOptionValue("cache_dir", "smarty");
		$this->template->config_dir = $this->config->getOptionValue("config_dir", "smarty");

		//nastavení cesty k pluginům smarty
		$this->template->plugins_dir = array('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'smarty' . DIRECTORY_SEPARATOR . 'libs'
											 . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR,
					   						 '.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'smarty' . DIRECTORY_SEPARATOR . 'plugins'
											 . DIRECTORY_SEPARATOR);

//		Pokud je debug tak vypnout kešování smarty
		if(self::$debugLevel >= 2){
			$this->template->force_compile = true;
			$this->template->clear_compiled_tpl();
		}
		
//		Vytvoření objektu šablon pro jádro
		$this->coreTpl = new Template();

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
			} else {
				new CoreException(_("Nepodařilo se načíst potřebnou systémovou třídu ").$className, 2);
			}
		}
		
//		Abstraktní třída pro JsPluginy
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'JsPlugins'. DIRECTORY_SEPARATOR . 'jsplugin.calss.php');
		
		
//		knihovny pro práci s chybami
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'Exceptions' . DIRECTORY_SEPARATOR . 'coreException.class.php');
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'Exceptions' . DIRECTORY_SEPARATOR . 'errors.class.php');
		
		// třída pro obsluhu autorizace
//		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'auth.class.php');
		//		třída pro obsluhu práv
//		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'rights.class.php');
		
		//		třída pro práci se zprávami modulů
//		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'messages.class.php');

		//		třída pro práci s hlavním menu
//		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'mainmenu.class.php');
		//		třída pro práci s šablonami
//		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'template.class.php');
	}
	
	/**
	 * Metoda inicializuje objekty pro práci s hláškami
	 *
	 */
	private function _initMessagesAndErrors()
	{
		//		Vytvoření objektu pro práci se zprávami
		$this->messages = new Messages(null, 'session', 'mmessages');
		$this->userErrors = new Messages();
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
	
	
	
	/**
	 * Metoda ověřuje autorizaci přístupu
	 */
	private function coreAuth() {
		$this->auth = new Auth($this->dbConnector, $this->config, $this->userErrors);
	}
	
	/**
	 * Metoda načte potřebné knihovny pro moduly
	 */
	private function loadModuleLibs() {
//		načtení cest modulů
//		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'routes.class.php');
//		načtení zvoleného článku modulu
//		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'article.class.php');
//		načtení akcí modulů
//		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'action.class.php');
//		načtení hlavních tříd modulu (controler, view)
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'controller.class.php');
//		třída pro práci s pohledem
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'view.class.php');
//		třída pro práci s parametry modulu
//		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'module.class.php');
//		třída pro práci s adresáři modulu
//		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'moduledirs.class.php');

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
	}
	
	/**
	 * Metoda přiřadí proměnou do šablony
	 * @param string -- název proměné v šabloně
	 * @param mixed -- hodnota
	 */
	private function assignVarToTpl($varName, $value) {
		$this->template->assign($varName, $value);
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
//		$this->_appMainDir = $appMainDir;
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
			$menu = new Menu($this->config, $this->dbConnector, $this->auth);
			
			$menu->controller();
			
			$menu->view();
			
			$this->assginTplObjToTpl($menu->getTemplate(), MainMenu::TPL_ARRAY_NAME);
			
		} else {
			new CoreException(_("Třída s hlavním menu neexistuje"), 1);
		}
		
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
//		$this->template->assign("MAIN_PAGE_TITLE", $this->config->getOptionValue("web_name"));
//		Template::addMainVar("MAIN_PAGE_TITLE", $this->config->getOptionValue("web_name"));
		$this->coreTpl->addVar("MAIN_PAGE_TITLE", $this->config->getOptionValue("web_name"));
		
		//adresa k rootu webu
//		$this->template->assign("MAIN_ROOT_DIR", self::getAppWebDir());
		$this->coreTpl->addVar("MAIN_ROOT_DIR", self::getAppWebDir());
		
		$link = new Links();
//		$link->getMainWebDir();
//		$this->template->assign("MAIN_WEB_DIR", $link->getMainWebDir());
		$this->coreTpl->addVar("MAIN_WEB_DIR", $link->getMainWebDir());
		$this->coreTpl->addVar("THIS_PAGE_LINK", $link);
		
//		cesty k obrázkům enginu
		$this->coreTpl->addVar("MAIN_IMAGES_PATH", $this->config->getOptionValue('images', 'dirs'));
		$this->coreTpl->addVar("MAIN_LANG_IMAGES_PATH", './'.$this->config->getOptionValue('images', 'dirs').'/'.$this->config->getOptionValue('images_lang', 'dirs').'/');
		
		unset($link);
		
//		$mainTpl->assign("MAIN_ROOT_DIR", $links->getMainWebDir());

		//Přihlášení uživatele
		$this->coreTpl->addVar("USER_IS_LOGIN", $this->auth->isLogin());
		$this->coreTpl->addVar("NOT_LOGIN_USER_NAME",_("Nepřihlášen"));
		$this->coreTpl->addVar("LOGIN_USER_NAME",_("Přihlášen"));
		$this->coreTpl->addVar("USER_LOGIN_USERNAME", $this->auth->getUserName());
		
		//Verze enginu
		$this->coreTpl->addVar("ENGINE_VERSION", $this->config->getOptionValue("engine_version"));

		//Debugovaci mod
		if (self::$debugLevel > 1){
			$this->coreTpl->addVar("DEBUG_MODE", true);
		}
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
		

		
		switch (AppCore::media()) {
			case AppCore::MEDIA_PRINT_TYPE:
				$this->template->display("index-print.tpl");;
			break;
			
			default:
				$this->template->display("index.tpl");
			break;
		}

		if(self::$debugLevel >= 3){
			echo "<pre style='text-align: left'>";
			print_r($this->template->get_template_vars());
			echo "</pre>";
		}
	}

	/**
	 * Metoda spouští moduly
	 * //TODO
	 */
	public function runModules() {
//		Načtení potřebných knihoven
		$this->loadModuleLibs();

		ModuleDirs::setWebDir(self::MAIN_ENGINE_PATH); //TODO patří přepsat tak aby se to zadávalo jinde
		ModuleDirs::setWebDataDir($this->config->getOptionValue("data_dir"));

		//načtení dat z db
		$itemsTable = $this->config->getOptionValue("items_table", Config::SECTION_DB_TABLES);
		$modulesTable = $this->config->getOptionValue("modules_table", Config::SECTION_DB_TABLES);
//		$userNameGroup = $this->auth->userdetail->offsetGet(Auth::USER_GROUP_NAME);
		$userNameGroup = $this->auth->getGroupName();

//		Vytvoření dotazu pro db
		$catItemsSelect = $this->dbConnector->select()
						   ->from(array("item" => $itemsTable))
						   ->join(array("module" => $modulesTable), "item.id_module=module.id_module", null)
						   ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup." LIKE \"r__\"")
						   ->where("item.id_category = '".Category::getId()."'", "and")
						   ->order("item.priority", "desc")
						   ->order("item.label");


//		$items = $this->dbConnector->fetchAssoc($catItemsSelect);
		$items = $this->dbConnector->fetchObjectArray($catItemsSelect);

		if($items != null){
//			Vytvoření objektu s článkem (article) //TODO pouze pro jeden článek
			$article = new Article();

			foreach ($items as $itemIndex => $item) {
				//			Vytvoření objektu šablony
				$template = new Template();
				
//				Příprava pro objekt modulu
//				Načtení všech tabulek databáze
				$tableIndex = 1; $moduleDbTables = array();
//				//TODO potřebuje optimalizaci a OPRAVIT
				$objectName=self::MODULE_DBTABLES_PREFIX.$tableIndex;
				while (isset($item->$objectName) AND ($item->$objectName != null)){
					$moduleDbTables[$tableIndex] = $item->$objectName;
					$tableIndex++;
					$objectName=self::MODULE_DBTABLES_PREFIX.$tableIndex;
				}

//				Vytvoření objektu pro práci s modulem
				$module = new Module($item, $moduleDbTables);
		
//				echo "<pre>";
//				print_r($item); //TODO vymazat
//				echo "</pre>";
				
				

				
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
					$action = new ModuleAction($module);
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
					//				zjištění cesty akce
//					echo $selectRoute = $routes->getRoute();
				}
				
//				načtení souboru s kontrolerem modulu
				if(file_exists('.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'controler.class.php')){
					require '.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'controler.class.php';
				} else {
					new CoreException(_("Nepodařilo se nahrát controler modulu ") . $module->getName(), 5);
				}


//				echo realpath('.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR."locales");
				
				// objekt modelu
				$model = null;
				
				//Jestli je kontroler spuštěn
				$isModuleControlerRun = false;

//				Příprava a nastavení použití překladu
				bindtextdomain($module->getName(), '.'.DIRECTORY_SEPARATOR.self::MODULES_DIR.DIRECTORY_SEPARATOR.$module->getName().DIRECTORY_SEPARATOR."locale");
				
				
				//			Vytvoření objektu kontroleru
				$controllerClassName = ucfirst($module->getName()).'Controller';
				if(class_exists($controllerClassName)){
//					Příprava a nastavení použití překladu
//					bindtextdomain(self::GETTEXT_DEFAULT_DOMAIN, '.'.DIRECTORY_SEPARATOR.self::MODULES_DIR.DIRECTORY_SEPARATOR.$module->getName().DIRECTORY_SEPARATOR."locales");
//					textdomain(self::GETTEXT_DEFAULT_DOMAIN); 
					
					$controller = new $controllerClassName($module, $this->config, $this->dbConnector, $action, $moduleRights, $this->messages, $this->userErrors, $article);

					//TODO není kompletí typ akce
					$actionCtrl = $action->getAction();
					
					/*
					 * Pokud je zadán jenom článek a žádná akce
					 * je použita výchozí akce (asi show)
					 */
					if(!$action->isAction() AND $article->isArticle()){
						$actionCtrl = $action->getDefaultAction();
					}
					
					$actionCtrl == null ? $actionCtrl=strtolower(self::MODULE_MAIN_CONTROLLER_PREFIX) : null;
					
					$routes->getRoute() != null ? $actionCtrl = ucfirst($actionCtrl) : null;

					$controllerAction = $routes->getRoute().$actionCtrl.self::MODULE_CONTROLLER_SUFIX;

//					Zvolení překladu na modul
					textdomain($module->getName()); 
					//			zvolení akce kontroleru
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
							textdomain(self::GETTEXT_DEFAULT_DOMAIN); 
							new CoreException(_("Action Controller ").$controllerAction._(" v modulu ") . $module->getName()._(" nebyl nalezen"), 11);
						}
					}

					$model = $controller->getModel();
					$changedView = $controller->getActionView();
					unset($controller);
					$isModuleControlerRun = true;
				} else {
					new CoreException(_("Nepodařilo se vytvořit objekt controleru modulu ") . $module->getName(), 6);
				}
				
//				Vrácení překladu na engine
				textdomain(self::GETTEXT_DEFAULT_DOMAIN); 

				//			načtení souboru s viewrem modulu
				if(file_exists('.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'view.class.php')){
					require '.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'view.class.php';
				} else {
					new CoreException(_("Nepodařilo se nahrát viewer modulu ") . $module->getName(), 7);
				}
				
//				Donastavení šablon
				$template->setModule($module);
				
				
				
//				Vytvoření objektu viewru
				$viewClassName = ucfirst($module->getName()).'View';
				if(class_exists($viewClassName) AND $isModuleControlerRun){
////					přepnutí překladu na modul
//					textdomain($module->getName()); 
										
					//				Volba pohledu
					if($changedView == null){
						$viewAction = $routes->getRoute().$actionCtrl.self::MODULE_VIEWER_SUFIX;
					} else {
						//TODO dořešit při zapnuté cestě
						$viewAction = $routes->getRoute().$changedView.self::MODULE_VIEWER_SUFIX;
					}
					
//					Vytvoření objektu pohledu
					$view = new $viewClassName($model, $module, $this->config, $moduleRights, $template);

					//	Zvolení překladu na modul
					textdomain($module->getName()); 
					
					if(($action->haveAction() OR $changedView != null) AND method_exists($view, $viewAction)){//TODO doladit jesli se správně dělají akce	
//					if(method_exists($controller, $controllerAction)){
						$view->$viewAction();
					}
					else if(!$action->isAction() AND $article->isArticle() AND method_exists($view, $viewAction)){ 
						$view->$viewAction();
					}  
					else if($defaultAction) {
						$view->mainView();
						if(!method_exists($view, $viewAction)){
							//				přepnutí překladu na engine
							textdomain(self::GETTEXT_DEFAULT_DOMAIN); 
							new CoreException(_("Action Viewer ").$viewAction._(" v modulu ") . $module->getName(). _(" nebyl nalezen"), 11);
						}		
					} else {
						if(!method_exists($view, $viewAction)){
							//				přepnutí překladu na engine
							textdomain(self::GETTEXT_DEFAULT_DOMAIN); 
							new CoreException(_("Action Viewer ").$viewAction._(" v modulu ") . $module->getName(). _(" nebyl nalezen"), 13);
						}
					}

				} else if($isModuleControlerRun) {
					new CoreException(_("Nepodařilo se vytvořit objekt view modulu ") . $module->getName(), 8);
				} else {
					new CoreException(_("Objekt view modulu nebyl vytvořen, protože nebyl vytvořen objekt kontroleru ") . $module->getName(), 14);
				}

//				přepnutí překladu na engine
				textdomain(self::GETTEXT_DEFAULT_DOMAIN); 

//				odstranění proměných
				unset($module);
				unset($model);
				unset($action);
				unset($routes);
				unset($controller);
				unset($view);
				
//				Uložení šablona a proměných do hlavní šablony
				$this->assginTplObjToTpl($template, 'MODULES_TEMPLATES');
				unset($template);

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
		
		$panelsTable = $this->config->getOptionValue("panels_table", "db_tables");
		$modulesTable = $this->config->getOptionValue("modules_table", "db_tables");
		$itemsTable = $this->config->getOptionValue("items_table", "db_tables");
		$categoryTable = $this->config->getOptionValue("category_table", "db_tables");
		
//		Načtení panelů u db
		$sqlSCelect = $this->dbConnector->select()
						   ->from(array("panel" => $panelsTable), "label")
						   ->join(array("items" => $itemsTable), "items.id_item=panel.id_item", null)
						   ->join(array("cat" => $categoryTable), "cat.id_category=items.id_category", null, "urlkey")
						   ->join(array("module" => $modulesTable), "items.id_module=module.id_module", null)
						   ->where("items.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$this->auth->getGroupName()." LIKE \"r__\"")
						   ->where("panel.position = '".$panelSideLower."'", "and")
						   ->where("panel.enable = ".(int)true, "and")
						   ->order("panel.priority", "desc");
						   
//						   fdsf
		$panelData = $this->dbConnector->fetchObjectArray($sqlSCelect);
		
//		echo "<pre>";
//		print_r($panelData);
//		echo "</pre>";
		
		if(!empty($panelData)){
//			$this->assignVarToTpl("MODULES_".$panelSideUpper."_PANEL", true);
				
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
				bindtextdomain($panelModule->getName(), '.'.DIRECTORY_SEPARATOR.self::MODULES_DIR.DIRECTORY_SEPARATOR.$panelModule->getName().DIRECTORY_SEPARATOR."locale");
				
				
				if(class_exists($panelClassName)){
//					Nastavení kategorie
					$category[Category::COLUM_CAT_URLKEY] = $panel->{Category::COLUM_CAT_URLKEY};
//					$category[Category::COLUM_CAT_ID] = $panel->{Category::COLUM_CAT_ID}; //TODO nejsou načítány z DB
//					$category[Category::COLUM_CAT_LABEL] = $panel->{Category::COLUM_CAT_LABEL};				

//					nastavení tempalte
					$panelTemplate->setModule($panelModule);
					$link = new Links(true);
					$panelTemplate->setTplCatLink($link->category($category[Category::COLUM_CAT_URLKEY]));
					


					$panel = new $panelClassName($panelModule, $this->dbConnector, $category, $this->messages, $this->userErrors, $panelTemplate, $panelRights);

//					spuštění controleru
					if(method_exists($panel, self::MODULE_PANEL_CONTROLLER)){
						textdomain($panelModule->getName()); //jazykové nastavení na modul
						$panel->{self::MODULE_PANEL_CONTROLLER}();
						textdomain(self::GETTEXT_DEFAULT_DOMAIN); //jazykové nastavení na engine
					} else {
						new CoreException(_("Neexistuje controler panelu ").$panelModule->getName().".",17);
					}

//					spuštění viewru
					if(method_exists($panel, self::MODULE_PANEL_VIEWER)){
						textdomain($panelModule->getName()); //jazykové nastavení na modul
						$panel->{self::MODULE_PANEL_VIEWER}();
						textdomain(self::GETTEXT_DEFAULT_DOMAIN); //jazykové nastavení na engine
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

//		$this->category = new Category($this->dbConnector, $this->config, $this->auth);
		Category::factory($this->dbConnector, $this->config, $this->auth);
		
		
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
	 * Hlavní metoda provádění aplikace
	 */
	public function runApp() {
		//autorizace přístupu
		$this->coreAuth();

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
	}
}

?>