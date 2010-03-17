<?php
/**
 * VVE FrameWork
 * Hlavní třída aplikace - singleton
 * Obsluhuje celou aplikaci a její komponenty a moduly.
 *
 * @copyright  Copyright (c) 2008 - 2009 Jakub Matas
 * @version    $Id$ VVE 5.0.0 $Revision$
 * @author     $Author$ $Date$
 *             $LastChangedBy$ $LastChangedDate$
 * @abstract 	Hlavní třída aplikace(Singleton)
 * @license    GNU General Public License v. 2 viz. Docs/license.txt
 * @internal   Last ErrorCode 22
 */

class AppCore {
   /**
    * Název enginu
    */
   const ENGINE_NAME = 'VVE';

   /**
    * Verze enginu
    */
   const ENGINE_VERSION = '6.0.0';
   /**
    * Obsahuje hlavní soubor aplikace
    */
   const APP_MAIN_FILE = 'index.php';

   /**
    * Konstanta s adresářem s moduly
    */
   const MODULES_DIR = "modules";

   /**
    * Adresář s knihovnami enginu
    */
   const ENGINE_LIB_DIR = 'lib';

   /**
    * Adresář s engine-pluginy
    */
   const ENGINE_EPLUINS_DIR = 'eplugins';

   /**
    * Adresář s JS-pluginy
    */
   const ENGINE_JSPLUINS_DIR = 'jsplugins';

   /**
    * Adset_magic_quotes_runtime(false);resář s helpery
    */
   const ENGINE_HELPERS_DIR = 'helpers';

   /**
    * Adresář s validátory
    */
   const ENGINE_VALIDATORS_DIR = 'validators';

   /**
    * Adresář s Modely enginu
    */
   const ENGINE_MODELS_DIR = 'models';

   /**
    * Adresář s konfigurací aplikace
    */
   const ENGINE_CONFIG_DIR = 'config';

   /**
    * Název konfiguračního souboru
    */
   const ENGINE_CONFIG_FILE = "config.php";

   /**
    * Adresář s ostatními pluginy
    */
   const ENGINE_PLUGINS_DIR = 'plugins';

   /**
    * Kešovací adresář pro dočasné soubory
    */
   const ENGINE_CACHE_DIR = 'cache';

   /**
    * Adresář pro knihovny z vyjímkami
    */
   const ENGINE_EXCEPTIONS_DIR = 'exceptions';

   /**
    * Adresář pro knihovny pro práci s ajaxem
    */
   const ENGINE_AJAX_DIR = 'ajax';

   /**
    * Adresář pro knihovny pro práci s šablonou
    */
   const ENGINE_TEMPLATE_DIR = 'templates';

   /**
    * Konstanta s názvem adresáře se specielními soubory (helpy, atd)
    */
   const SPECIALITEMS_DIR = 'specialitems';

   /**
    * Název třídy pro sitemapu -- sufix
    */
   const MODULE_SITEMAP_SUFIX_CLASS = 'SiteMap';

   /**
    * Název třídy pro hledání search -- sufix
    */
   const MODULE_SEARCH_SUFIX_CLASS = 'Search';

   /**
    * Instance hlavní třídy
    * @var AppCore
    */
   private static $_coreInstance = null;

   /**
    * Hlavní adresář aplikace
    * @var string
    */
   private static $_appWebDir = null;

   /**
    * Adresář s knihovnami
    * @var string
    */
   private static $_appLibDir = null;

   /**
    * Objekt pro přístup k šabloně v jádře
    * @var Template
    */
   private $coreTpl = null;

   /**
    * Objekt s hláškami modulů
    * @var Messages
    */
   private static $messages = null;

   /**
    * Objekt s chybovými hláškami modulů (NE VYJÍMKY!)
    * @var Messages
    */
   private static $userErrors = null;

   /**
    * Objekt Autorizace
    * @var Auth
    */
   private static $auth = null;

   /**
    * objekt URL požadavku (základní)
    * @var Url_Request
    */
   private static $urlRequest = null;

   /**
    * Objekt současné kategorie
    * @var Category
    */
   private static $category = null;

   /**
    * Proměná pro sledování doby generování scriptu
    * @var float
    */
   private $_startTime = null;

   /**
    * Proměná obsahuje jestli je zobrazeny chybová stránka
    * @var boolean
    */
   static private $isErrorPage = true;

   /*
    * MAGICKÉ METODY
   */

   /**
    * Konstruktor objektu AppCore
    * @todo prověřit, protože né vždy se správně přiřadí cesta, pravděpodobně BUG php
    */
   private function __construct() {
      //		Definice globálních konstant
      define('URL_SEPARATOR', '/');
      define('VVE_APP_IS_RUN', true);
      // verze PHP
      if(!defined('PHP_VERSION_ID')){
         $version = explode('.',PHP_VERSION);
         define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
      }
      set_magic_quotes_runtime(false); // magic quotes OFF !!
      if(get_magic_quotes_gpc() === 1) trigger_error("Magic quotes is Enable, please disable this feature");
      require_once AppCore::getAppWebDir().self::ENGINE_CONFIG_DIR.DIRECTORY_SEPARATOR.self::ENGINE_CONFIG_FILE;


      //		inicializace stratovacího času
      List ($usec, $sec) = Explode (' ', microtime());
      $this->_startTime=((float)$sec + (float)$usec);

      // inicializace parametrů jádra a php
      $this->_initCore();

      //	nastavení hlavního adresáře aplikace
      /*
         * @todo prověřit, protože né vždy se správně přiřadí cesta, pravděpodobně BUG php
      */
//      $direName = dirname(__FILE__); // OLD version + dává někdy špatný výsledek
//      $realPath = realpath($direName); // OLD version + dává někdy špatný výsledek
      // $realPath = dirname(__FILE__); // ověřit v php 5.3.0 lze použít __DIR__

      //	přidání adresáře pro načítání knihoven
      set_include_path('./lib/' . PATH_SEPARATOR . get_include_path());

      //načtení potřebných knihoven
      $this->_loadLibraries();

      // inicializace db konektoru
      $this->_initDb();

      // načtení systémového konfiguračního souboru
      $this->_initConfig();

      //		inicializace URL
      Url_Request::factory();

      // inicializace Šablonovacího systému
      Template::factory();

      //inicializace lokalizace
      Locale::factory();

      //		inicializace sessions
      Sessions::factory(VVE_SESSION_NAME);
      // výběr jazyka a locales
      Locale::selectLang();
      //		Inicializace chybových hlášek
      $this->_initMessagesAndErrors();

      //autorizace přístupu
      $this->_initAuth();
      //Spuštění jádra aplikace
      $this->runCore();
   }

   /**
    * Třída je singleton
    * není povolen clone
    */
   public function __clone() {
      throw new BadMethodCallException(_('Není povoleno inicializovat více jak jednu třídu aplikace'), 1);
   }

   /*
    * STATICKÉ METODY
   */

   /**
    * Singleton instance objektu
    *
    * @return AppCore
    */
   public static function getInstance() {
      if (null === self::$_coreInstance) {
         self::$_coreInstance = new self();
      } else {
         throw new BadMethodCallException(_('Objekt aplikace byl již vytvořen'), 2);
      }
      return self::$_coreInstance;
   }

   /**
    * Metoda vrací adresář aplikace
    * @return string -- adresář aplikace
    */
   public static function getAppWebDir() {
      return self::$_appWebDir;
   }

   /**
    * Metoda vrací adresář aplikace s knihovnami
    * @return string -- adresář aplikace s knihovnami
    */
   public static function getAppLibDir() {
      return self::$_appLibDir;
   }

   /**
    * Metoda vrací cestu k cache adresáři aplikace
    * @return string
    */
   public static function getAppCacheDir() {
      return self::getAppWebDir().self::ENGINE_CACHE_DIR.DIRECTORY_SEPARATOR;
   }

   /**
    * Metoda vygeneruje instanci aplikace
    * pokud již instance existuje, bude vyhozena vyjímka
    * Instance aplikace je singleton
    */
   public static function createApp() {
      self::getInstance();
   }

   /**
    * Metoda nastavuje že má být zobrazena chybová stránka 404
    * @param boolean $var (option) zapne chybovou stránku
    */
   public static function setErrorPage($var = true) {
      self::$isErrorPage = $var;
   }

   /**
    * Metooda vrací jestli je nastaveny chybová stránka 404
    * @return boolean -- true pokud je zobrazena
    */
   public static function isErrorPage() {
      return self::$isErrorPage;
   }

   /**
    * Metoda vrací objekt pro zprávy modulů
    * @return Messages -- objekt zpráv
    */
   public static function &getInfoMessages() {
      return self::$messages;
   }

   /**
    * Metoda vrací objekt pro chybové zprávy modulů
    * @return Messages -- objekt zpráv
    */
   public static function &getUserErrors() {
      return self::$userErrors;
   }

   /**
    * Metoda vrací objekt autorizace
    * @return Auth -- objekt autorizace
    */
   public static function getAuth() {
      return self::$auth;
   }

   /**
    * Metoda vrací objekt url požadavku
    * @return Url_Request  -- objekt autorizace
    */
   public static function getUrlRequest() {
      return self::$urlRequest;
   }

   /**
    * Metoda vrací objekt hlavní kategorie
    * @return Category
    */
   public static function getCategory() {
      return self::$category;
   }

   /*
    * PRIVÁTNÍ METODY
   */

   /**
    * Metoda inicializuje základní nastavení jádra systému
    */
   private function _initCore() {
      // nastavení mb kodování na UTF protože celá aplikace pracuje s UTF
      mb_internal_encoding("UTF-8");
   }

   /**
    * Metoda inicializuje připojení k databázi
    */
   private function _initDb() {
      try {
         // inicializace PDO
         Db_PDO::factory();
      } catch (PDOException $e) {
         new CoreErrors($e);
      }
   }

   /**
    * Metoda inicializuje konfiguraci s konfiguračního souboru
    *
    */
   private function _initConfig() {
      $cfgModel = new Model_Config();
      $cfgVals = $cfgModel->getConfigStat();
      while ($cfg = $cfgVals->fetch()) {
         if(!defined('VVE_'.$cfg[Model_Config::COLUMN_KEY])) {
            if($cfg[Model_Config::COLUMN_VALUE] == 'true') {
               define(strtoupper('VVE_'.$cfg[Model_Config::COLUMN_KEY]), true);
            } else if($cfg[Model_Config::COLUMN_VALUE] == 'false') {
               define(strtoupper('VVE_'.$cfg[Model_Config::COLUMN_KEY]), false);
            } else {
               define(strtoupper('VVE_'.$cfg[Model_Config::COLUMN_KEY]), $cfg[Model_Config::COLUMN_VALUE]);
            }
         }
      }
   }

   /**
    * Metoda načte potřebné knihovny
    * @todo refaktoring nutný
    */
   private function _loadLibraries() {
      /**
       * Funkce slouží pro automatické načítání potřebných tříd
       * @param string -- název třídy
       */
      function __autoload($classOrigName) {
         $file = strtolower($classOrigName).'.class.php';
         $classL = strtolower($classOrigName);
         $pathDirs = explode('_', $classL);
         $moduleFile = $pathDirs[count($pathDirs)-1].'.class.php';
         $pathFull = implode('/', $pathDirs);
         unset ($pathDirs[count($pathDirs)-1]);
         $pathShort = implode('/', $pathDirs);

         if(file_exists(AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR.$file)) {
            require_once AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR.$file;
         } else if(file_exists(AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR
         .DIRECTORY_SEPARATOR.$classL.DIRECTORY_SEPARATOR.$file)) {
            require_once AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR
                            .$classL.DIRECTORY_SEPARATOR.$file;
         } else if(file_exists(AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR
         .DIRECTORY_SEPARATOR.$pathShort.DIRECTORY_SEPARATOR.$file)) {
            require_once AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR
                            .DIRECTORY_SEPARATOR.$pathShort.DIRECTORY_SEPARATOR.$file;
         } else if(file_exists(AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR
         .DIRECTORY_SEPARATOR.$pathFull.DIRECTORY_SEPARATOR.$file)) {
            require_once AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR
                            .DIRECTORY_SEPARATOR.$pathFull.DIRECTORY_SEPARATOR.$file;
         } else if(file_exists(AppCore::getAppLibDir().AppCore::MODULES_DIR
         .DIRECTORY_SEPARATOR.$pathShort.DIRECTORY_SEPARATOR.$moduleFile)) {
            require_once AppCore::getAppLibDir().AppCore::MODULES_DIR
                            .DIRECTORY_SEPARATOR.$pathShort.DIRECTORY_SEPARATOR.$moduleFile;
         } else {
            trigger_error(_("Chybějící třída").": ".$classOrigName." ".$file." module-file: ".$moduleFile);
         }
      }
      //		knihovny pro práci s chybami
      require_once (AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR . DIRECTORY_SEPARATOR
                      . AppCore::ENGINE_EXCEPTIONS_DIR . DIRECTORY_SEPARATOR . 'coreException.class.php');
      require_once (AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR . DIRECTORY_SEPARATOR
                      . AppCore::ENGINE_EXCEPTIONS_DIR . DIRECTORY_SEPARATOR . 'moduleException.class.php');
      require_once (AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR . DIRECTORY_SEPARATOR
                      . AppCore::ENGINE_EXCEPTIONS_DIR . DIRECTORY_SEPARATOR . 'dbException.class.php');
      require_once (AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR . DIRECTORY_SEPARATOR
                      . AppCore::ENGINE_EXCEPTIONS_DIR . DIRECTORY_SEPARATOR . 'badClassException.class.php');
      require_once (AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR . DIRECTORY_SEPARATOR
                      . AppCore::ENGINE_EXCEPTIONS_DIR . DIRECTORY_SEPARATOR . 'badFileException.class.php');
      require_once (AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR . DIRECTORY_SEPARATOR
                      . AppCore::ENGINE_EXCEPTIONS_DIR . DIRECTORY_SEPARATOR . 'imageException.class.php');
      // soubor s globálními funkcemi, které nejsou součástí php, ale časem by mohly být
      require_once (AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR . DIRECTORY_SEPARATOR
                      . 'functions' . DIRECTORY_SEPARATOR . 'functions.php');
   }

   /**
    * Metoda inicializuje objekty pro práci s hláškami
    */
   private function _initMessagesAndErrors() {
      set_error_handler('CoreErrors::errorHandler');
      //		Vytvoření objektu pro práci se zprávami
      self::$messages = new Messages('session', 'messages', true);
      self::$userErrors = new Messages('session', 'errors');
   }

   /**
    * Metoda ověřuje autorizaci přístupu
    * @deprecated -- odstranit, lze přistupovat přímo přes statické funkceS
    */
   private function _initAuth() {
      self::$auth = new Auth();
   }

   /*
    * VEŘEJÉ METODY
   */

   /**
    * Metoda nastavuje hlavní adresář aplikace
    * @param string -- hlavní adresář aplikace
    */
   public static function setAppMainDir($appMainDir) {
      self::$_appWebDir = $appMainDir.DIRECTORY_SEPARATOR;
   }

   /**
    * Metoda nastavuje hlavní adresář s knihovnami aplikace
    * @param string -- hlavní adresář s knihovnami aplikace
    */
   public static function setAppMainLibDir($appMainLibDir) {
      self::$_appLibDir = $appMainLibDir.DIRECTORY_SEPARATOR;
   }

   /**
    * Metoda vytvoří hlavní menu aplikace
    */
   public function createMainMenu() {
      Menu_Main::factory();
      try {
         if(!file_exists(AppCore::getAppWebDir().AppCore::ENGINE_CONFIG_DIR . DIRECTORY_SEPARATOR
         . 'menu.class.php')) {
            $menuFile = AppCore::getAppLibDir().AppCore::MODULES_DIR . DIRECTORY_SEPARATOR
                    . 'menu.class.php';
         } else {
            $menuFile = AppCore::getAppWebDir().AppCore::ENGINE_CONFIG_DIR . DIRECTORY_SEPARATOR
                    . 'menu.class.php';
         }
         require_once $menuFile;
         if(!class_exists("Menu", false)) {
            throw new BadClassException(_('Třídu pro tvorbu menu se nepodařilo načíst'),6);
         }
         $menu = new Menu();
         $menu->controller();
         $menu->view();
         $this->coreTpl->menuObj = $menu->template();
      } catch (Exception $e ) {
         new CoreErrors($e);
      }
   }

   /**
    * Metoda přiřadí do šablony hlavní proměnné systému
    */
   public function assignMainVarsToTemplate() {
      //	Hlavni promene strany
      $this->coreTpl->rootDir = self::getAppWebDir();
      $this->coreTpl->debug = VVE_DEBUG_LEVEL;
      $this->coreTpl->setPVar("mainWebDir", Url_Request::getBaseWebDir());
      $this->coreTpl->setPVar("imagesDir", Template::face().Template::IMAGES_DIR.URL_SEPARATOR);
      $this->coreTpl->mainLangImagesPath = VVE_IMAGES_LANGS_DIR.URL_SEPARATOR;
      $this->coreTpl->pageKeywords = Category::getSelectedCategory()->getCatDataObj()->{Model_Category::COLUMN_KEYWORDS};
      $this->coreTpl->pageDesc = Category::getSelectedCategory()->getCatDataObj()->{Model_Category::COLUMN_DESCRIPTION};
      //Přihlášení uživatele
      $this->coreTpl->userIsLogin = Auth::isLogin();
      $this->coreTpl->userLoginUsername = Auth::getUserName();
      // Přiřazení jazykového pole
      $this->coreTpl->setPVar("appLangsNames", Locale::getAppLangsNames());
      // Vytvoření odkazů s jazyky
      $langs = array();
      $langNames = Locale::getAppLangsNames();
      $link = new Url_Link();
      foreach (Locale::getAppLangs() as $langKey => $lang) {
         $langArr = array();
         $langArr['name'] = $lang;
         $langArr['label'] = $langNames[$lang];
         if($lang != Locale::getDefaultLang()) {
            $langArr['link'] = (string)$link->lang($lang);
         } else {
            $langArr['link'] = (string)$link->lang();
         }
         array_push($langs, $langArr);
      }
      unset($langNames);
      unset($link);
      unset($langArr);
      $this->coreTpl->setPVar("appLangs", $langs);
      unset($langs);
      $this->coreTpl->setPVar("appLang", Locale::getLang());
   }

   /**
    * metoda vyrenderuje šablonu
    * @todo Předělat, protože se nekontroluje existence jiných typů médií
    */
   public function renderTemplate() {
      //		načtení doby zpracovávání aplikace
      List ($usec, $sec) = Explode (' ', microtime());
      $endTime = ((float)$sec + (float)$usec);
      $this->coreTpl->execTime = round($endTime-$this->_startTime, 4);
      $this->coreTpl->countAllSqlQueries = Db_PDO::getCountQueries();
      $this->coreTpl->addTplFile(Template_Core::getMainIndexTpl(), true);
      // render šablony
      print($this->coreTpl);
   }

   /**
    * Metoda spouští moduly
    */
   public function runModule() {
      try {
         // načtení a kontrola cest u modulu
         $routesClassName = ucfirst(self::getCategory()->getModule()->getName()).'_Routes';
         if(!class_exists($routesClassName)) {
            throw new BadClassException(sprintf(_("Nepodařilo se načíst třídu '%s' cest (routes) modulu."),
            self::getCategory()->getModule()->getName()), 10);
         }
         //					Vytvoření objektu kontroleru
         $routes = new $routesClassName(self::$urlRequest->getModuleUrlPart());
         // kontola cest
         $routes->checkRoutes();
         if(!$routes->getActionName()) {
            AppCore::setErrorPage();
            return false;
         }

         // načtení kontroleru
         if($routes->getClassName() == null) {
            $controllerClassName = ucfirst(self::getCategory()->getModule()->getName()).'_Controller';
         } else {
            $controllerClassName = ucfirst($routes->getClassName()).'_Controller';
         }
         if(!class_exists($controllerClassName)) {
            throw new BadClassException(sprintf(_("Nepodařilo se načíst třídu '%s' controleru modulu."),
            self::getCategory()->getModule()->getName()), 10);
         }
         //					Vytvoření objektu kontroleru
         $controller = new $controllerClassName(self::getCategory(), $routes);
         $controller->runCtrl();

         // přiřazení šablony do výstupu
         $this->coreTpl->module = $controller->_getTemplateObj();
      } catch (Exception $e) {
         new CoreErrors($e);
      }
   }

   /**
    * Metoda spustí samotný požadavek na modul, např generování listu v xml souboru
    */
   public function runModuleOnly() {
      if(self::$urlRequest->getUrlType() == Url_Request::URL_TYPE_MODULE_REQUEST) {
         // spuštění modulu
         try {
            if(!self::getCategory() instanceof Category OR self::getCategory()->getCatDataObj() == null) {
               throw new CoreException(sprintf(_("Špatně zadaný požadavek \"%s\" na modul"),
               self::$urlRequest->getAction().'.'.self::$urlRequest->getOutputType()));
            }
            // načtení a kontrola cest u modulu
            $routesClassName = ucfirst(self::getCategory()->getModule()->getName()).'_Routes';
            if(!class_exists($routesClassName)) {
               throw new BadClassException(sprintf(_("Nepodařilo se načíst třídu cest (routes) modulu \"%s\"."),
               self::getCategory()->getModule()->getName()), 10);
            }
            //	Vytvoření objektu s cestama modulu
            $routes = new $routesClassName(self::$urlRequest->getModuleUrlPart());
//            $routes = new Routes();
            // kontola cest
            $routes->checkRoutes();
            if(!$routes->getActionName()) {
               AppCore::setErrorPage();
               return false;
            }
            // načtení kontroleru
            $controllerClassName = ucfirst(self::getCategory()->getModule()->getName()).'_Controller';
            if(!class_exists($controllerClassName)) {
               trigger_error(sprintf(_("Nepodařilo se načíst třídu controleru modulu \"%s\"."),
                       self::getCategory()->getModule()->getName()), 10);
            }
            //					Vytvoření objektu kontroleru
            $controller = new $controllerClassName(self::getCategory(), $routes);
            $controller->runCtrlAction($routes->getActionName(), self::$urlRequest->getOutputType());

            if(in_array(self::$urlRequest->getOutputType(), Template_Output::getHtmlTypes())){
               $controller->_getTemplateObj()->renderTemplate();
            }
         } catch (Exception $e ) {
            new CoreErrors($e);
         }
      } else if(self::$urlRequest->getUrlType() == Url_Request::URL_TYPE_MODULE_STATIC_REQUEST) {
         // načtení a kontrola cest u modulu
         $className = ucfirst(self::$urlRequest->getName()).'_Controller';
         $classNameV = ucfirst(self::$urlRequest->getName()).'_View';
         $methodName = self::$urlRequest->getAction().'Controller';
         $methodNameV = self::$urlRequest->getAction().'View';
         if(method_exists($className,$methodName)) {
            call_user_func($className."::".$methodName);
            if(method_exists($classNameV,$methodNameV)) {
               call_user_func($classNameV."::".$methodNameV);
            }
         } else {
            trigger_error(sprintf(_('Neimplementovaná statická akce "%s" modulu'),$className."::".$methodName));
         }
      }

   }

   /**
    * Metoda inicializuje a spustí panel
    * @param string $side -- jaký panel je spuštěn (left, right, bottom, top, ...)
    * @todo dodělat implementaci ostatních pozic panelů
    */
   public function runPanels() {
      $this->coreTpl->panels = array();
      // vygenerování pole pro šablony panelů
      $panelPositions = vve_parse_cfg_value(VVE_PANEL_TYPES);
      foreach ($panelPositions as $panel) {
         $this->coreTpl->panels[$panel] = array();
      }
      $panelsM = new Model_Panel();
      // výběr jestli se zpracovávají individuální panely nebo globální
      if(self::$category->isIndividualPanels()) {
         $panels = $panelsM->getPanelsList(self::$category->getId());
      } else {
         $panels = $panelsM->getPanelsList();
      }

      foreach ($panels as $panel) {
         $panelCat = new Category(null, false, $panel);
         if(!file_exists(AppCore::getAppLibDir().self::MODULES_DIR.DIRECTORY_SEPARATOR
         .$panelCat->getModule()->getName().DIRECTORY_SEPARATOR.'panel.class.php')) {
            continue;
         }
         // načtení a kontrola cest u modulu
         $routesClassName = ucfirst($panelCat->getModule()->getName()).'_Routes';
         if(!class_exists($routesClassName)) {
            throw new BadClassException(sprintf(_("Nepodařilo se načíst třídu '%s' cest (routes) modulu."),
            $panelCat->getModule()->getName()), 10);
         }
         //					Vytvoření objektu kontroleru
         $routes = new $routesClassName(null);
         $controllerClassName = ucfirst($panelCat->getModule()->getName()).'_Panel';
         if(!class_exists($controllerClassName)) {
            throw new BadClassException(sprintf(_("Nepodařilo se načíst třídu '%s' controleru panelu modulu."),
            self::getCategory()->getModule()->getName()), 10);
         }

         //					Vytvoření objektu kontroleru
         $panelController = new $controllerClassName($panelCat, $routes);
         $panelController->run();
         array_push($this->coreTpl->panels[(string)$panel->{Model_Panel::COLUMN_POSITION}],$panelController->_getTemplateObj());
      }
   }

   /**
    * Metoda přiřadí chyby do šablony
    */
   public function assignCoreErrorsToTpl() {
      $this->coreTpl->coreErrors = CoreErrors::getErrors();
      $this->coreTpl->coreErrorsEmpty = CoreErrors::isEmpty();
   }

   /**
    * Metoda přiřadí zprávy šablonovacího systému
    */
   public function assignMessagesToTpl() {
      $this->coreTpl->messages = self::getInfoMessages()->getMessages();
      $this->coreTpl->moduleErrors = self::getUserErrors()->getMessages();
      // výmaz uložených zpráv (kvůli requestů je tady)
      self::getInfoMessages()->eraseSavedMessages();
      self::getUserErrors()->eraseSavedMessages();
   }

   /**
    * Metoda vytváří sitemapu a odesílá ji na výstup
    */
   public function runSitemap() {
      // načtení kategorií a podle nich vytahání a vytvoření pododkazů
      $cats = new Model_Category();
      $categories = $cats->getCategoryList();

      // odkaz na titulní stranu
      SiteMap::addMainPage();

      foreach ($categories as $category) {
         $catObj = new Category(null, false, $category);
         $routesClassName = ucfirst($catObj->getModule()->getName()).'_Routes';
         if(!class_exists($routesClassName)) {
            $routes = new Routes(null);
         } else {
            $routes = new $routesClassName(null);
         }
         if(!file_exists(AppCore::getAppLibDir().self::MODULES_DIR.DIRECTORY_SEPARATOR
         .$catObj->getModule()->getName().DIRECTORY_SEPARATOR.'sitemap.class.php')) {
            $sitemap = new SiteMap($catObj, $routes, $category->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_FREQ},
                    $category->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_PRIORITY});
            // vložení alespoň změny kategorie
            $sitemap->addCategoryItem(new DateTime($category->{Model_Category::COLUMN_CHANGED}));
         } else {
            $sClassName = ucfirst($catObj->getModule()->getName()).'_Sitemap';
            $sitemap = new $sClassName($catObj, $routes, $category->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_FREQ},
                    $category->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_PRIORITY});
         }
         $sitemap->run();
         unset ($sitemap);
      }
      SiteMap::generateMap(self::$urlRequest->getOutputType());
   }

   /**
    * Metoda spouští kód pro generování specielních stránek
    */
   public function runSpecialPage() {
      $this->coreTpl->specialPage = true;
      switch (self::$urlRequest->getName()) {
         case 'sitemap':
            $this->runSitemapPage();
            break;
         default:
            break;
      }
   }

   /**
    * Metoda spouští kód pro generování specielních stránek
    */
   public function runSupportServices() {
      // test jestli se prování html nebo jiný formát (html je vypsáno přímo ve stránce)
      switch (self::$urlRequest->getName()) {
         case 'sitemap':
            $this->runSitemap();
            break;
      }
   }

   /**
    * Metoda spouští generování stránky s mapou webu
    */
   public function runSitemapPage() {
      $sitemapTpl = new Template(new Url_Link(true));
      $sitemapTpl->addTplFile('sitemap.phtml');
      $sitemapTpl->setPVar('CURRENT_CATEGORY_PATH', array(_('mapa stránek')));
      $sitemapTpl->categories = Menu_Main::getMenuObj();

      // načtení kategorií a podle nich vytahání a vytvoření pododkazů
      $cats = new Model_Category();
      $categories = $cats->getCategoryList();

      $catArr = array();
      foreach ($categories as $category) {
         $catObj = new Category(null, false, $category);
         $routesClassName = ucfirst($catObj->getModule()->getName()).'_Routes';
         if(!class_exists($routesClassName)) {
            $routes = new Routes(null);
         } else {
            $routes = new $routesClassName(null);
         }
         if(!file_exists(AppCore::getAppLibDir().self::MODULES_DIR.DIRECTORY_SEPARATOR
            .$catObj->getModule()->getName().DIRECTORY_SEPARATOR.'sitemap.class.php')) {
               $sitemap = new SiteMap($catObj, $routes,false);
         } else {
            $sClassName = ucfirst($catObj->getModule()->getName()).'_Sitemap';
            $sitemap = new $sClassName($catObj, $routes,false);
         }
         $sitemap->run();
         $catArr[$catObj->getId()] = $sitemap->createMapArray();
      }
      $sitemapTpl->catArr = $catArr;
      $this->coreTpl->specialPageTpl = $sitemapTpl;
   }

   /**
    * Metoda spustí akci nad JsPluginem
    */
   public function runJsPlugin() {
      $pluginName = 'JsPlugin_'.ucfirst(self::$urlRequest->getName());
      $jsPlugin = new $pluginName();
      // vytvoření souboru
      $jsPlugin->runAction(self::$urlRequest->getAction(), self::$urlRequest->getUrlParams(),
              self::$urlRequest->getOutputType());
   }

   /**
    * Metoda pro spuštění akce na componentě
    */
   public function runComponent() {
      $componentName = 'Component_'.ucfirst(self::$urlRequest->getName());
      $component = new $componentName();
      $component->runAction(self::$urlRequest->getAction(), self::$urlRequest->getUrlParams(),
              self::$urlRequest->getOutputType());
   }

   /**
    * Metoda načte soubor se specialními vlastnostmi přenesenými do šablony,
    * které jsou jednotné pro celý web
    * @todo předělat pro načítání z adresáře Webu, ne knihoven
    */
   private function initialWebSettings() {
      $fileName = 'initial'.ucfirst(self::$urlRequest->getOutputType()).'.php';
      if(file_exists(AppCore::getAppWebDir().self::ENGINE_CONFIG_DIR.DIRECTORY_SEPARATOR.$fileName)) {
         require AppCore::getAppWebDir().self::ENGINE_CONFIG_DIR.DIRECTORY_SEPARATOR.$fileName;
      }
   }

   /**
    * Hlavní metoda provádění aplikace
    */
   public function runCore() {
      if(VVE_DEBUG_LEVEL >= 3 AND function_exists('xdebug_start_trace')){
         xdebug_start_trace(AppCore::getAppCacheDir().'trace.log');
      }
      self::$urlRequest = new Url_Request();
      // zapnutí buferu podle výstupu
      Template_Output::factory(self::$urlRequest->getOutputType());
      if(!Template_Output::isBinaryOutput()){
         if (defined('VVE_USE_GZIP') AND VVE_USE_GZIP == true AND
            isset ($_SERVER['HTTP_ACCEPT_ENCODING']) AND substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){
            ob_start("ob_gzhandler");
         } else {
            ob_start();
         }
      }
      Locale::setLang(self::$urlRequest->getUrlLang());

      // načtení kategorie
      self::$category = new Category(self::$urlRequest->getCategory(),true);
      Url_Link::setCategory(self::$category->getUrlKey());
      if(!self::getCategory()->isValid()) {
         AppCore::setErrorPage();
      }
      if(!self::$urlRequest->isFullPage()) {
         // vynulování chyby, protože chybová stránka je výchozí stránka
         AppCore::setErrorPage(false);
         // je zpracovává pouze požadavek na část aplikace
         // vybrání části pro zpracování podle požadavku
         switch (self::$urlRequest->getUrlType()) {
            case Url_Request::URL_TYPE_MODULE_REQUEST:
            case Url_Request::URL_TYPE_MODULE_STATIC_REQUEST:
               $this->runModuleOnly();
               break;
            case Url_Request::URL_TYPE_SUPPORT_SERVICE:
               $this->runSupportServices();
               break;
            case Url_Request::URL_TYPE_COMPONENT_REQUEST:
               $this->runComponent();
               break;
            case Url_Request::URL_TYPE_JSPLUGIN_REQUEST:
               $this->runJsPlugin();
               break;
         }

         if(AppCore::isErrorPage()) {
            trigger_error(_("Neplatný požadavek na aplikaci"), E_USER_ERROR);
         }
         // render chyb
         if(!CoreErrors::isEmpty()) {
            CoreErrors::printErrors();
         }
      } else {
         // je zpracovávána stránka aplikace
         $this->coreTpl = new Template_Core();

         // Globální inicializace proměných do šablony
         $this->initialWebSettings();
         //vytvoření hlavního menu
         $this->createMainMenu();

         if(self::$urlRequest->getUrlType() == Url_Request::URL_TYPE_ENGINE_PAGE
                 AND !AppCore::isErrorPage()) {
            // zpracování stránky enginu (sitemap, search, atd.)
            $this->runSpecialPage();
         } else if(!AppCore::isErrorPage()) {
            // přiřazení kategorrie do nadpisu
            $this->coreTpl->addPageHeadline(Category::getSelectedCategory()->getLabel());
            $this->coreTpl->categoryId = Category::getSelectedCategory()->getId();
            // zpracovávní modulu
            $this->runModule();
            $this->coreTpl->setPVar('CURRENT_CATEGORY_PATH',
                    Menu_Main::getMenuObj()->getPath(Category::getSelectedCategory()->getId()));
         }

         // =========	spuštění panelů
         $this->runPanels();

         // pokud je chyba vykreslíme chybovou stránku
         if(AppCore::isErrorPage()) {
            $this->coreTpl->setPVar('CURRENT_CATEGORY_PATH', array(_('stránka nenalezena')));
            $this->coreTpl->pageNotFound = true;
         }
         //	Přiřazení hlášek do šablony
         $this->assignMessagesToTpl();
         // vložení proměných šablony z jadra
         $this->assignMainVarsToTemplate();
         // přiřazení chybových hlášek jádra do šablony
         $this->assignCoreErrorsToTpl();
         //	render šablony
         $this->renderTemplate();
      }
      if(AppCore::isErrorPage()) {
         Template_Output::addHeader("HTTP/1.0 404 Not Found");
      }
      if(!Template_Output::isBinaryOutput()){
         $content = ob_get_contents();
         // odeslání potřebných hlaviček a délky řetězců
         Template_Output::setContentLenght(strlen($content));
      }
      // odeslání hlaviček
      Template_Output::sendHeaders();
      if(VVE_DEBUG_LEVEL >= 3 AND function_exists('xdebug_stop_trace')){
         xdebug_stop_trace();
      }
      if(!Template_Output::isBinaryOutput()){
         ob_end_flush();
      }
      return true;
   }
}
?>