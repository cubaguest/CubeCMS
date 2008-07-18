<?php
/**
 * Vypecky Engine
 *
 * @category   VypeckyEnginy
 * @package    Main Application file
 * @copyright  Copyright (c) 2005-2008 Jakub Matas
 * @version    $Id: app.php 3.0.0 alpha1 10.7.2008
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
	const DEBUG = true;

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
	 * Instance hlavní třídy
	 * @var AppCoreClass
	 */
	private static $_coreInstance = null;

	/**
	 * Hlavní adresář aplikace
	 * @var string
	 */
	private static $_appWebDir = null;
	
	/**
	 * Hlavní objekt šablony
	 * @var smarty
	 */
	public $template = null;

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

//		inicializace odkazů
		$this->_initLinks();
		
		//inicializace lokalizace
		$this->_initLocale();

		$this->_initDbConnector();
		
		$this->_initMessagesAndErrors();

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
		$locales = array("cs" => "cs_CZ.UTF-8", "en" => "en_US");

		if (!isset($_GET["lang"])){
//			include './lang/'.DEFAULT_LANG.'.php';
			$selectLang=$this->config->getOptionValue("defaultlang");
			//	Links::setLang(null);
		} else {
//			if (file_exists("./lang/".$_GET["lang"].".php")){
//				include './lang/'.$_GET["lang"].'.php';
//			} else {
//				include './lang/'.DEFAULT_LANG.'.php';
//				$errorEngine->addMessage($errMsg[101], 101);
//			}
			$selectLang=$_GET["lang"];
			//Nastaveni jazyku pro generovaní url
//			Links::setLang($selectLang);
		}

		//	nastavení gettext a locales
		putenv("LANG=$locales[$selectLang]");
		setlocale(LC_ALL, $locales[$selectLang]);

		bindtextdomain("messages", "./locales");
		textdomain("messages");
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
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'smarty' . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR
					   . 'Smarty.class.php');

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
		if(self::DEBUG){
			$this->template->force_compile = true;
			$this->template->clear_compiled_tpl();
		}

	}
	
	/**
	 * Metoda načte potřebné knihovny
	 */
	private function _loadLibraries() {
//		knihovny pro práci s chybami
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'Exceptions' . DIRECTORY_SEPARATOR . 'coreException.class.php');
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'Exceptions' . DIRECTORY_SEPARATOR . 'errors.class.php');
		
		// třída pro obsluhu autorizace
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'auth.class.php');
		//		třída pro obsluhu práv
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'rights.class.php');
		
		//		třída pro práci se zprávami modulů
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'messages.class.php');
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
	 * Metoda ověřuje autorizaci přístupu
	 */
	private function coreAuth() {
		$this->auth = new Auth($this->dbConnector, $this->config);
	}
	
	/**
	 * Metoda načte potřebné knihovny pro moduly
	 */
	private function loadModuleLibs() {
//		načtení cest modulů
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'routes.class.php');
//		načtení zvoleného článku modulu
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'article.class.php');
//		načtení akcí modulů
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'action.class.php');
//		načtení hlavních tříd modulu (controler, view)
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'controller.class.php');
//		třída pro práci s pohledem
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'view.class.php');
//		třída pro práci s parametry modulu
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'module.class.php');
//		třída pro práci s adresáři modulu
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'moduledirs.class.php');

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
//	SELECT s.id_section, s.label as slabel, s.alt as salt, s.id_section, cat.id_category, cat.urlkey, cat.label as clabel, cat.alt
//	FROM ".MYSQL_TABEL_PREFIX.MYSQL_CATEGORY_TABLE." cat
//	JOIN ".MYSQL_TABEL_PREFIX.MYSQL_SECTIONS_TABLE." s ON s.id_section = cat.id_section
//	JOIN ".MYSQL_TABEL_PREFIX.MYSQL_CATEGORY_ITEMS_TABLE." items ON cat.id_category = items.id_category
//	WHERE (".$userNameGroup." LIKE \"r__\" AND cat.active = 1 )
//	GROUP BY cat.id_category
//	ORDER BY s.priority DESC, slabel, cat.priority DESC, clabel";

		$catTable = $this->config->getOptionValue("category_table", "db_tables");
		$secTable = $this->config->getOptionValue("section_table", "db_tables");
		$itemsTable = $this->config->getOptionValue("items_table", "db_tables");
//		$userNameGroup = $this->auth->userdetail->offsetGet(Auth::USER_GROUP_NAME);
		$userNameGroup = $this->auth->getGroupName();

		$menuSelect = $this->dbConnector->select()
						   ->from(array("cat" => $catTable), array("urlkey", "clabel" => "label", "id_category", "alt"))
						   ->join(array("s" => $secTable), "s.id_section = cat.id_section", null, array("id_section", "slabel" => "label", "salt" => "alt"))
						   ->join(array("item" => $itemsTable), "cat.id_category = item.id_category", null, null)
						   ->where(Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup." LIKE \"r__\"")
						   ->where("cat.active = 1", "and")
						   ->group("cat.id_category")
						   ->order("s.priority", "desc")
						   ->order("slabel")
						   ->order("cat.priority", "desc")
						   ->order("clabel");


		$menuArray = $this->dbConnector->fetchAssoc($menuSelect);

		$categoryArray = array();
		$sectionArray = array();
		$oldIdSection = null;

		//		vytvoření odkazu
		$link = new Links(true);
		
		if($menuArray){
			foreach ($menuArray as $index => $item) {
				$item["url"]=(string)$link->category($item["urlkey"]);
				if($item["id_section"] != $oldIdSection){
					$oldIdSection = $item["id_section"];
					$categoryArray[$oldIdSection] = array();
					array_push($categoryArray[$oldIdSection], $item);
					$sectionArray[$item["id_section"]] = $item;
				} else {
					array_push($categoryArray[$oldIdSection], $item);
				}
			}
		} else {
			new CoreException("Cannot load main menu from db", 2);
		}

//		odstranění odkazu sekci s jednou kategorii
		foreach ($categoryArray as $key => $value) {
			if(sizeof($categoryArray[$key]) == 1){
				unset($categoryArray[$key]);
				$sectionArray[$key]["submenu"] = false;
			} else {
				$sectionArray[$key]["submenu"] = true;
			}
		}

//		přiřazení do šablony
		$this->template->assign("MAIN_MENU_SECTION_ARRAY", $sectionArray);
		$this->template->assign("MAIN_MENU_CATEGORY_ARRAY", $categoryArray);
		
		unset($link);
	}

	/**
	 * Metoda přiřadí do šablony hlavní proměnné systému
	 */
	public function assignMainVarsToTemplate() {
		//	Hlavni promene strany
		$this->template->assign("MAIN_PAGE_TITLE", $this->config->getOptionValue("web_name"));
//		$mainTpl->assign("PAGE_TITLE", $engineMsg[1]);
//		$mainTpl->assign("PAGE_TITLE_ONLY", $engineMsg[2]);

		//adresa k rootu webu
		$this->template->assign("MAIN_ROOT_DIR", self::getAppWebDir());
		
		$link = new Links();
//		$link->getMainWebDir();
		$this->template->assign("MAIN_WEB_DIR", $link->getMainWebDir());
		unset($link);
		
//		$mainTpl->assign("MAIN_ROOT_DIR", $links->getMainWebDir());
			
		
		//Verze enginu
		$this->template->assign("ENGINE_VERSION", $this->config->getOptionValue("engine_version"));

		//Debugovaci mod
		$this->template->assign("DEBUG_MODE", self::DEBUG);
	}

	/**
	 * metoda vyrenderuje šablonu
	 */
	public function renderTemplate() {
//		načtení doby zpracovávání aplikace
		List ($usec, $sec) = Explode (' ', microtime());
		$endTime = ((float)$sec + (float)$usec);
		$this->template->assign("MAIN_EXEC_TIME", round($endTime-$this->_stratTime, 4));
//		$this->template->assign("COUNT_ALL_SQL_QUERY", $mysql->getCountAllSqlQuery());

		$this->template->display("index.tpl");
	}

	/**
	 * Metoda spouští moduly
	 * //TODO
	 */
	public function runModules() {
//		Načtení potřebných knihoven
		$this->loadModuleLibs();

//		ModuleDirs::setWebDir($this->_appMainDir);
		ModuleDirs::setWebDir(self::MAIN_ENGINE_PATH); //TODO patří přepsat tak aby se to zadávalo jinde
		ModuleDirs::setWebDataDir($this->config->getOptionValue("data_dir"));

//		$this->catAction = new Action(self::ACTION_GET);

		//načtení dat z db
		$itemsTable = $this->config->getOptionValue("items_table", "db_tables");
		$modulesTable = $this->config->getOptionValue("modules_table", "db_tables");
//		$userNameGroup = $this->auth->userdetail->offsetGet(Auth::USER_GROUP_NAME);
		$userNameGroup = $this->auth->getGroupName();

//		Vytvoření dotazu pro db
		$catItemsSelect = $this->dbConnector->select()
						   ->from(array("item" => $itemsTable))
						   ->join(array("module" => $modulesTable), "item.id_module=module.id_module", null)
						   ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup." LIKE \"r__\"")
						   ->where("item.id_category = '".$this->category->getId()."'", "and")
						   ->order("item.priority", "desc")
						   ->order("item.label");


//		$items = $this->dbConnector->fetchAssoc($catItemsSelect);
		$items = $this->dbConnector->fetchObjectArray($catItemsSelect);

		if($items != null){
//			Vytvoření objektu s článkem (article) //TODO pouze pro jeden článek
			$article = new Article();

			foreach ($items as $itemIndex => $item) {
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
//				while (array_key_exists(self::MODULE_DBTABLES_PREFIX.$tableIndex, $item) AND ($item[self::MODULE_DBTABLES_PREFIX.$tableIndex] != null)) {
//					$moduleDbTables[$tableIndex] = $item[self::MODULE_DBTABLES_PREFIX.$tableIndex];
//					$tableIndex++;
//				}

//				Vytvoření objektu pro práci s modulem
//				$module = new Module($item["id_item"], $item["id_module"], $item["name"], $item["label"],$item["alt"], $item["datadir"], $moduleDbTables, $item["params"]);
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
					new CoreException(_("Cannot load module action ") . $module->getName(), 12);
				}
				
				$action = null;
				if(class_exists("ModuleAction")){
					$action = new ModuleAction($module);
				}
				
//				načtení souboru s cestami (routes) modulu
				if(file_exists('.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'routes.class.php')){
					require '.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'routes.class.php';
				} else {
					new CoreException(_("Cannot load module routes ") . $module->getName(), 10);
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
					new CoreException(_("Cannot load module controler ") . $module->getName(), 5);
				}

				
				// objekt modelu
				$model = null;
				
				//			Vytvoření objektu kontroleru
				$controllerClassName = ucfirst($module->getName()).'Controller';
				if(class_exists($controllerClassName)){
					$controller = new $controllerClassName($module, $this->config, $this->dbConnector, $action, $moduleRights, $this->messages, $this->userErrors);

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
					$viewAction = $routes->getRoute().$actionCtrl.self::MODULE_VIEWER_SUFIX;

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
						if(!method_exists($controller, $controllerAction)){
							new CoreException(_("Action Controller ").$controllerAction._(" v modulu ") . $module->getName()._(" nebyl nalezen"), 11);
						}
						$controller->mainController();
						$defaultAction = true;
					}

					$model = $controller->getModel();
					unset($controller);

				} else {
					new CoreException(_("Cannot create module controler object ") . $module->getName(), 6);
				}

				//			načtení souboru s viewrem modulu
				if(file_exists('.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'view.class.php')){
					require '.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'view.class.php';
				} else {
					new CoreException(_("Cannot load module viewer ") . $module->getName(), 7);
				}
//
//				//			Vytvoření objektu kontroleru
				$viewClassName = ucfirst($module->getName()).'View';
				if(class_exists($viewClassName)){
					$view = new $viewClassName($model, $module, $this->config);

					if($action->haveAction() AND method_exists($view, $viewAction)){//TODO doladit	
//					if(method_exists($controller, $controllerAction)){
						$view->$viewAction();
					}
					else if(!$action->isAction() AND $article->isArticle() AND method_exists($view, $viewAction)){ 
						$view->$viewAction();
					}  
					else if($defaultAction) {
						if(!method_exists($view, $viewAction)){
							new CoreException(_("Action Viewer ").$viewAction._(" v modulu ") . $module->getName(). _(" nebyl nalezen"), 11);
						}
						$view->mainView();
					} else {
						if(!method_exists($view, $viewAction)){
							new CoreException(_("Action Viewer ").$viewAction._(" v modulu ") . $module->getName(). _(" nebyl nalezen"), 11);
						}
					}
					
//					$viewAction = $this->catAction->getAction();
					//			zvolení akce kontroleru
//					if($item["id_item"] == $this->catAction->getId() AND method_exists($view, $this->catAction->getAction())){
//						$view->$viewAction();
//					} else {
//						$view->mainView();
//					}
				} else {
					new CoreException(_("Nepodařilo se vytvořit objekt view modulu ") . $module->getName(), 8);
				}


//				odstranění proměných
				unset($module);

			}
		} else {
			new CoreException(_("Nepodařilo se nahrát prvky kategorie z databáze."), 9);
		}
	}

	/**
	 * metoda vybere, která kategorie je vybrána a uloží je di objektu kategorie
	 */
	public function selectCategory() {
		require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'category.class.php');

		$this->category = new Category($this->dbConnector, $this->config, $this->auth);
	}

	public function assignCoreErrorsToTpl() {
		$this->template->assign("CORE_ERRORS", CoreException::getAllExceptions());
		$this->template->assign("CORE_ERRORS_EMPTY", CoreException::isEmpty());

//		$coreExceptions = CoreException::getAllExceptions();
	}

	/**
	 * Metoda přiřadí všechny proměnné do šablonovacího systému
	 * //TODO není omplementována, vytvořit načítání do šablony.
	 */
	public function assignModuleVarsToTpl() {
		$this->template->assign("MODULES_STYLESHEET", Template::getStylesheets());
		$this->template->assign("MODULES_JAVASCRIPT", Template::getJavaScripts());
		$this->template->assign("MODULES_TEMPLATES", Template::getCategoryItems());
		$this->template->assign("MESSAGES", $this->messages->getMessages());
		$this->template->assign("ERRORS", $this->userErrors->getMessages());		
		
//		echo "<pre>";
//		print_r(Template::getCategoryItems());
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