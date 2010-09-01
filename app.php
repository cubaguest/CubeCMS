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
   const ENGINE_VERSION = 6.2;
   /**
    * Obsahuje hlavní soubor aplikace
    */
   const APP_MAIN_FILE = 'index.php';

   /**
    * Konstanta s adresářem s moduly
    */
   const MODULES_DIR = "modules";

   /**
    * Konstanta s adresářem s dokumentací
    */
   const DOCS_DIR = "docs";

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
//      if(PHP_VERSION_ID < 5.3){ // protože existují kreténi, kteří mají na php 5.3 zapnuté magic quotes
//         set_magic_quotes_runtime(false); // magic quotes OFF !!
         if(get_magic_quotes_gpc() === 1){
            // odstranněí všech backslashes
            function magicQuotes_awStripslashes(&$value, $key) {$value = stripslashes($value);}
            $gpc = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST, &$_SERVER);
            array_walk_recursive($gpc, 'magicQuotes_awStripslashes');
            //trigger_error("Magic quotes is Enable, please disable this feature");
         }
//      }

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
      Locales::factory();

      //		inicializace sessions
      Sessions::factory(VVE_SESSION_NAME);
      // výběr jazyka a locales
      Locales::selectLang();
      //		Inicializace chybových hlášek
      $this->_initMessagesAndErrors();
      // kontrola verze
      $this->checkCoreVersion();

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
    * Metoda vrací cestu k datovému adresáři aplikace
    * @return string
    */
   public static function getAppDataDir() {
      return self::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR;
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
    * Metoda provádí kontrolu verze jádra
    */
   private function checkCoreVersion(){
      /* Upgrade jádra */
      if(defined('VVE_VERSION') AND VVE_VERSION != self::ENGINE_VERSION){ // kvůli neexistenci předchozí detekce
         $core = new Install_Core();
         $core->upgrade();
      } else if(!defined('VVE_VERSION')) {
         $settings = new Model_Config();
         $settings->saveCfg('VERSION', self::ENGINE_VERSION, Model_Config::TYPE_STRING, 'Verze jádra', true);
         $link = new Url_Link(true);
         self::getInfoMessages()->addMessage(_('Jádro bylo násilně aktualizováno na novou verzi. Kontaktuje webmastera, protože nemusí pracovat správně!'));
         $link->clear(true)->reload();
      } else {
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
         } else if(file_exists(AppCore::getAppLibDir().AppCore::MODULES_DIR
         .DIRECTORY_SEPARATOR.$pathFull.DIRECTORY_SEPARATOR.$moduleFile)) {
            require_once AppCore::getAppLibDir().AppCore::MODULES_DIR
                            .DIRECTORY_SEPARATOR.$pathFull.DIRECTORY_SEPARATOR.$moduleFile;
         } else {
//            var_dump($file, $classL, $pathDirs, $moduleFile, $pathFull, $pathShort);
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
   public function createMenus() {
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
      // inicializace admin menu
      if(Auth::isLogin()){
         Menu_Admin::factory();
         $menu = new Menu_Admin();
         $menu->controller();
         $menu->view();
         $this->coreTpl->menuAdminObj = $menu->template();
      }

   }

   /**
    * Metoda přiřadí do šablony hlavní proměnné systému
    */
   public function assignMainVarsToTemplate() {
      //	Hlavni promene strany
      $this->coreTpl->debug = VVE_DEBUG_LEVEL;
      $this->coreTpl->mainLangImagesPath = VVE_IMAGES_LANGS_DIR.URL_SEPARATOR;
      // Přiřazení jazykového pole
      $this->coreTpl->setPVar("appLangsNames", Locales::getAppLangsNames());
      // Vytvoření odkazů s jazyky
      $langs = array();
      $langNames = Locales::getAppLangsNames();
      if(count($langNames) > 1){
         $link = new Url_Link();
         foreach (Locales::getAppLangs() as $langKey => $lang) {
            $langArr = array();
            $langArr['name'] = $lang;
            $langArr['label'] = $langNames[$lang];
            if($lang != Locales::getDefaultLang()) {
               $langArr['link'] = (string)$link->lang($lang);
            } else {
               $langArr['link'] = (string)$link->lang();
            }
            array_push($langs, $langArr);
         }
         unset($link);
         unset($langArr);
      }
      unset($langNames);
      $this->coreTpl->setPVar("appLangs", $langs);
      unset($langs);
      $this->coreTpl->setPVar("appLang", Locales::getLang());
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
//      file_put_contents(AppCore::getAppWebDir().'logs'.DIRECTORY_SEPARATOR.'time.log',
//              $this->coreTpl->execTime."\n", FILE_APPEND); // export rychlosti
      $this->coreTpl->countAllSqlQueries = Db_PDO::getCountQueries();
      $this->coreTpl->addTplFile(Template_Core::getMainIndexTpl(), true);
      // render šablony
      print($this->coreTpl);
   }

   /**
    * Metoda spouští moduly
    */
   public function runModule() {
      $this->coreTpl->categoryId = Category::getSelectedCategory()->getId();
      try {
         // načtení a kontrola cest u modulu
         $routesClassName = ucfirst(self::getCategory()->getModule()->getName()).'_Routes';
         if(!class_exists($routesClassName)) {
            throw new BadClassException(sprintf(_("Nepodařilo se načíst třídu '%s' cest (routes) modulu."),
            self::getCategory()->getModule()->getName()), 10);
         }
         //					Vytvoření objektu kontroleru
         $routes = new $routesClassName(self::$urlRequest->getModuleUrlPart(), self::getCategory());
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
    * Metoda spouští rss export na modulu
    */
   public function runModuleRss() {
      if(self::$category->haveFeed() == false){
         AppCore::setErrorPage(true);
         return false;
      }

      if(!file_exists(AppCore::getAppLibDir().self::MODULES_DIR.DIRECTORY_SEPARATOR
         .self::getCategory()->getModule()->getName().DIRECTORY_SEPARATOR.'rss.class.php')){
         throw new BadClassException(sprintf(_("Nepodařilo se načíst třídu pro zpracování rss zdroje modulu \"%s\"."),
         self::getCategory()->getModule()->getName()), 10);
      }

      // načtení a kontrola cest u modulu
      $routesClassName = ucfirst(self::getCategory()->getModule()->getName()).'_Routes';
      if(!class_exists($routesClassName)) {
         throw new BadClassException(sprintf(_("Nepodařilo se načíst třídu cest (routes) modulu \"%s\"."),
         self::getCategory()->getModule()->getName()), 10);
      }
      //	Vytvoření objektu s cestama modulu
      $routes = new $routesClassName(self::$urlRequest->getModuleUrlPart(),self::getCategory());

      $rssClassName = ucfirst(self::getCategory()->getModule()->getName()).'_Rss';
      $rssCore = new $rssClassName(self::getCategory(), $routes);

      $rssCore->runController();
      $rssCore->runView();
   }

   /**
    * Metoda spustí samotný požadavek na modul, např generování listu v xml souboru
    */
   public function runModuleOnly() {
      if(self::$urlRequest->getUrlType() == Url_Request::URL_TYPE_MODULE_REQUEST) {
         // spuštění modulu
         try {
            if(!self::getCategory() instanceof Category_Core OR self::getCategory()->getCatDataObj() == null) {
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
         } catch (Exception $e ) {
            new CoreErrors($e);
            return false;
         }
         if(AppCore::getUrlRequest()->isXHRRequest() AND $routes->getRespondClass() != null){
            // render odpovědi pro XHR
            $class = $routes->getRespondClass();
            $respond = new $class();
            // přenos dat z šablony do dat odeslaných v respond
            $respond->setData($controller->_getTemplateObj()->getTemplateVars());
            $respond->renderRespond();
         } else if(in_array(self::$urlRequest->getOutputType(), Template_Output::getHtmlTypes())){
            /*
             * TODO -- tohle by se mělo dořešit, protože není jisté jestli tu má být vůbec render
             */
            // render normálního výpisu
            if(Template_Core::getMainIndexTpl() == Template_Core::INDEX_DEFAULT_TEMPLATE){
               $controller->_getTemplateObj()->renderTemplate();
            } else {
               // render při změně indexu
               $this->coreTpl = new Template_Core();
               $this->coreTpl->module = $controller->_getTemplateObj();
               $this->renderTemplate();
            }
         }
      } else if(self::$urlRequest->getUrlType() == Url_Request::URL_TYPE_MODULE_STATIC_REQUEST) {
         // načtení a kontrola cest u modulu
         $className = ucfirst(self::$urlRequest->getName()).'_Controller';
         $classNameV = ucfirst(self::$urlRequest->getName()).'_View';
         $methodName = self::$urlRequest->getAction().'Controller';
         $methodNameV = self::$urlRequest->getAction().'View';
         if(method_exists($className,$methodName)) {
            $result = call_user_func($className."::".$methodName);
            if($result !== false AND method_exists($classNameV,$methodNameV)) {
               $result = call_user_func($classNameV."::".$methodNameV);
            }
            if($result === false) AppCore::setErrorPage(true);
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
    * Metoda spouští kód pro generování specielních stránek
    */
   public function runCoreModule() {
      $className = 'Module_'.ucfirst(self::$category->getModule()->getName());
      if(!AppCore::isErrorPage() AND class_exists($className)){
         $ctrl = new $className(self::$category);
      } else {
         $ctrl = new Module_ErrPage(self::$category);
      }
      $ctrl->runController(self::$urlRequest->getOutputType());
      // view metoda
      $viewM = 'run'.ucfirst(self::$urlRequest->getOutputType()).'View';
      if(method_exists($ctrl, $viewM) AND self::$urlRequest->getOutputType() != 'html'){
         $ctrl->{$viewM}();
      } else {
         $ctrl->runView();
         $this->coreTpl->module = $ctrl->template();
      }
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
      // načtení kategorie
      $className = 'Module_'.ucfirst(self::$urlRequest->getCategory()).'_Category';
      if(self::$urlRequest->getUrlType() == Url_Request::URL_TYPE_CORE_MODULE AND class_exists($className)){
         self::$category = new $className(self::$urlRequest->getCategory(),true);
         unset ($className);
      } else if(
//         self::$urlRequest->getUrlType() == Url_Request::URL_TYPE_NORMAL AND
         ((self::$urlRequest->getRequestUrl() == '' AND self::$urlRequest->getCategory() == null)
            OR (self::$urlRequest->getRequestUrl() != '' AND self::$urlRequest->getCategory ()!= null))
         ) {
         self::$category = new Category(self::$urlRequest->getCategory(),true);
         Url_Link::setCategory(self::$category->getUrlKey());
      } else { // Chyba stránky
         self::$category = new Module_ErrPage_Category(self::$urlRequest->getCategory(),true);
         Url_Link::setCategory(self::$category->getUrlKey());
         AppCore::setErrorPage(true);
      }

      if(!self::$urlRequest->isFullPage()) {
         // vynulování chyby, protože chybová stránka je výchozí stránka
         AppCore::setErrorPage(false);
         // je zpracovává pouze požadavek na část aplikace
         // vybrání části pro zpracování podle požadavku
         switch (self::$urlRequest->getUrlType()) {
            case Url_Request::URL_TYPE_MODULE_REQUEST:
            case Url_Request::URL_TYPE_MODULE_STATIC_REQUEST:
               $ret = $this->runModuleOnly();
               break;
            case Url_Request::URL_TYPE_MODULE_RSS:
               $ret = $this->runModuleRss();
               break;
            case Url_Request::URL_TYPE_CORE_MODULE:
               $ret = $this->runCoreModule();
               break;
            case Url_Request::URL_TYPE_COMPONENT_REQUEST:
               $ret = $this->runComponent();
               break;
            case Url_Request::URL_TYPE_JSPLUGIN_REQUEST:
               $ret = $this->runJsPlugin();
               break;
         }
         if($ret === false){
            AppCore::setErrorPage(true);
         }
         if(self::$urlRequest->getUrlType() != Url_Request::URL_TYPE_MODULE_RSS){
            if(AppCore::isErrorPage()) {
               trigger_error(_("Neplatný požadavek na aplikaci"), E_USER_ERROR);
            }
            // render chyb
            if(!CoreErrors::isEmpty()) {
               CoreErrors::printErrors();
            }
         }
      }
      if(self::$urlRequest->isFullPage() 
         OR (AppCore::isErrorPage() AND self::$urlRequest->getUrlType() == Url_Request::URL_TYPE_MODULE_RSS)){
//      } else {
         // je zpracovávána stránka aplikace
         $this->coreTpl = new Template_Core();

         // Globální inicializace proměných do šablony
         $this->initialWebSettings();
         //vytvoření hlavního menu
         $this->createMenus();

         if(self::$urlRequest->getUrlType() == Url_Request::URL_TYPE_NORMAL AND !AppCore::isErrorPage()) {
            // zpracovávní modulu
            $this->runModule();
            if(Menu_Main::getMenuObj() != null){ // kontrola prázdného menu
               $this->coreTpl->setPVar('CURRENT_CATEGORY_PATH',
                    Menu_Main::getMenuObj()->getPath(Category::getSelectedCategory()->getId()));
            }
         }
         
         if(self::$urlRequest->getUrlType() == Url_Request::URL_TYPE_CORE_MODULE
                 OR AppCore::isErrorPage()) {
            // zpracování stránky enginu (sitemap, rss, error, atd.)
            $this->runCoreModule();
         }

         // =========	spuštění panelů
         $this->runPanels();

         //	Přiřazení hlášek do šablony
         $this->assignMessagesToTpl();
         // vložení proměných šablony z jadra
         $this->assignMainVarsToTemplate();
         // přiřazení chybových hlášek jádra do šablony
         $this->assignCoreErrorsToTpl();
         //	render šablony
         $this->renderTemplate();
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