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
 * @abstract   Hlavní třída aplikace(Singleton)
 * @license    GNU General Public License v. 2 viz. Docs/license.txt
 * @internal   Last ErrorCode 22
 */
if (!defined('CUBECMS_LIB_DIR')) {
   define('CUBECMS_LIB_DIR', 'lib');
}
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . CUBECMS_LIB_DIR . DIRECTORY_SEPARATOR . 'defines.php';
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . CUBECMS_LIB_DIR . DIRECTORY_SEPARATOR . 'loader.class.php';
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . CUBECMS_LIB_DIR . DIRECTORY_SEPARATOR . 'trobject.class.php';
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . CUBECMS_LIB_DIR . DIRECTORY_SEPARATOR . 'debug' . DIRECTORY_SEPARATOR . 'debug.class.php';

class AppCore extends TrObject {

   /**
    * Název enginu
    */
   const ENGINE_NAME = 'Cube CMS';

   /**
    * Verze enginu
    */
   const ENGINE_VERSION = '8.4.3';

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
   const ENGINE_LIB_DIR = CUBECMS_LIB_DIR;

   /**
    * Adresář s logy enginu
    */
   const ENGINE_LOGS_DIR = 'logs';

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
   public static $runVars = array(
      'SIMULATE_XHR',
      'DEBUG',
      'FACEPREVIEW',
   );

   /*
    * MAGICKÉ METODY
    */

   /**
    * Konstruktor objektu AppCore
    * @todo prověřit, protože né vždy se správně přiřadí cesta, pravděpodobně BUG php
    */
   private function __construct()
   {
      //    inicializace stratovacího času
      List ($usec, $sec) = Explode(' ', microtime());
      $this->_startTime = ((float) $sec + (float) $usec);
      //    Definice globálních konstant
      define('URL_SEPARATOR', '/');
      // verze PHP
      if (!defined('PHP_VERSION_ID')) {
         $version = explode('.', PHP_VERSION);
         define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
         define('PHP_MAJOR_VERSION', $version[0]);
         define('PHP_MINOR_VERSION', $version[1]);
         define('PHP_RELEASE_VERSION', $version[2]);
      }
//      if(PHP_VERSION_ID < 5.3){ // protože existují kreténi, kteří mají na php 5.3 zapnuté magic quotes
//         set_magic_quotes_runtime(false); // magic quotes OFF !!
      if (get_magic_quotes_gpc() === 1) {

         // odstranněí všech backslashes
         function magicQuotes_awStripslashes(&$value, $key)
         {
            $value = stripslashes($value);
         }

         $gpc = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST, &$_SERVER);
         array_walk_recursive($gpc, 'magicQuotes_awStripslashes');
         //trigger_error("Magic quotes is Enable, please disable this feature");
      }
//      }
      if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
         define('SERVER_PLATFORM', 'UNIX');
      } else {
         define('SERVER_PLATFORM', 'WIN');
      }
      // inicializace parametrů jádra a php
      $this->_initCore();

      // bootstrap
      if (is_file(AppCore::getAppWebDir() . 'config' . DIRECTORY_SEPARATOR . 'bootstrap.php')) {
         include_once AppCore::getAppWebDir() . 'config' . DIRECTORY_SEPARATOR . 'bootstrap.php';
      }
   }

   /**
    * Třída je singleton
    * není povolen clone
    */
   public function __clone()
   {
      throw new BadMethodCallException($this->tr('Není povoleno inicializovat více jak jednu třídu aplikace'));
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
         throw new BadMethodCallException('Objekt aplikace byl již vytvořen');
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
    * Metoda vrací adresář aplikace s knihovnami
    * @return string -- adresář aplikace s knihovnami
    */
   public static function getAppLibDir()
   {
      return self::$_appLibDir;
   }

   /**
    * Metoda vrací cestu k datovému adresáři aplikace
    * @return string
    */
   public static function getAppDataDir()
   {
      return self::getAppWebDir() . CUBE_CMS_DATA_DIR . DIRECTORY_SEPARATOR;
   }

   /**
    * Metoda vrací cestu k cache adresáři aplikace
    * @return string
    */
   public static function getAppCacheDir()
   {
      return self::getAppWebDir() . self::ENGINE_CACHE_DIR . DIRECTORY_SEPARATOR;
   }

   /**
    * Metoda vrací cestu k log adresáři aplikace
    * @return string
    */
   public static function getAppLogDir()
   {
      return self::getAppWebDir() . self::ENGINE_LOGS_DIR . DIRECTORY_SEPARATOR;
   }

   /**
    * Metoda vygeneruje instanci aplikace
    * pokud již instance existuje, bude vyhozena vyjímka
    * Instance aplikace je singleton
    */
   public static function createApp()
   {
      return self::getInstance();
   }

   /**
    * Metoda nastavuje že má být zobrazena chybová stránka 404
    * @param boolean $var (option) zapne chybovou stránku
    */
   public static function setErrorPage($var = true)
   {
      if ($var) {
         self::$category = new Module_ErrPage_Category(null, true);
      }
      self::$isErrorPage = $var;
   }

   /**
    * Metooda vrací jestli je nastaveny chybová stránka 404
    * @return boolean -- true pokud je zobrazena
    */
   public static function isErrorPage()
   {
      return self::$isErrorPage;
   }

   /**
    * Metoda vrací objekt pro zprávy modulů
    * @return Messages -- objekt zpráv
    */
   public static function &getInfoMessages()
   {
      return self::$messages;
   }

   /**
    * Metoda vrací objekt pro chybové zprávy modulů
    * @return Messages -- objekt zpráv
    */
   public static function &getUserErrors()
   {
      return self::$userErrors;
   }

   /**
    * Metoda vrací objekt url požadavku
    * @return Url_Request  -- objekt autorizace
    * @deprecated use Url_Request::getInstance()
    */
   public static function getUrlRequest()
   {
      return Url_Request::getInstance();
   }

   /**
    * Metoda vrací objekt hlavní kategorie
    * @return Category
    */
   public static function getCategory()
   {
      return self::$category;
   }

   /*
    * PRIVÁTNÍ METODY
    */

   /**
    * Metoda inicializuje základní nastavení jádra systému
    */
   private function _initCore()
   {
      // přidání adresáře pro načítání knihoven
      set_include_path(AppCore::getAppLibDir() . self::ENGINE_LIB_DIR . DIRECTORY_SEPARATOR . PATH_SEPARATOR . get_include_path());

      // Autoloaders
      spl_autoload_extensions('.class.php,.php');
      // spl_autoload_register(); PHP 5.3.10 make warnings here
      Loader::loadCache();
      spl_autoload_register(array('Loader', 'cacheAutoLoader'));
      spl_autoload_register(array('Loader', 'libAutoLoader'));
      spl_autoload_register(array('Loader', 'moduleAutoLoader'));
      // base classes
      $this->_initBaseClasses();

      date_default_timezone_set('Europe/Prague');
      // nastavení mb kodování na UTF protože celá aplikace pracuje s UTF
      mb_internal_encoding("UTF-8");
      if (version_compare(PHP_VERSION, '5.6.0', '<')) {
         iconv_set_encoding('input_encoding', 'UTF-8');
         iconv_set_encoding('output_encoding', 'UTF-8');
         iconv_set_encoding('internal_encoding', 'UTF-8');
      }
      ini_set("default_charset", "utf-8");
      // max upload Limit
      $max_upload = vve_parse_size(ini_get('upload_max_filesize'));
      $max_post = vve_parse_size(ini_get('post_max_size'));
      $memory_limit = vve_parse_size(ini_get('memory_limit'));
      define('VVE_MAX_UPLOAD_SIZE', min($max_upload, $max_post, $memory_limit));
      define('CUBE_CMS_MAX_UPLOAD_SIZE', min($max_upload, $max_post, $memory_limit));
   }

   /**
    * Metoda načte základní třídy
    */
   private function _initBaseClasses()
   {
      // exceptions
      $exFiles = array('coreException', 'dbException', 'badClassException', 'badFileException',
         'imageException', 'badRequestException', 'controllerException', 'UnexpectedPageException', 'ModuleException',
         'UnauthorizedAccessException', 'ForbiddenAccessException');
      foreach ($exFiles as $exFile) {
         require_once (AppCore::getAppLibDir() . AppCore::ENGINE_LIB_DIR . DIRECTORY_SEPARATOR
            . AppCore::ENGINE_EXCEPTIONS_DIR . DIRECTORY_SEPARATOR . $exFile . '.class.php');
      }
      // logování
      require_once (AppCore::getAppLibDir() . AppCore::ENGINE_LIB_DIR . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'log.class.php'); // loger
      // soubor s globálními funkcemi, které nejsou součástí php, ale časem by mohly být
      require_once (AppCore::getAppLibDir() . AppCore::ENGINE_LIB_DIR . DIRECTORY_SEPARATOR
         . 'functions' . DIRECTORY_SEPARATOR . 'functions.php');
      // ostatní často načítané třídy                
   }

   /**
    * Metoda inicializuje připojení k databázi
    */
   private function _initDb()
   {
      Db_PDO::factory();
   }

   /**
    * Metoda inicializuje konfiguraci s konfiguračního souboru
    *
    */
   private function _initConfig()
   {
      $cfgModel = new Model_ConfigGlobal();
//       $recs = $cfgModel->columns(array(Model_Config::COLUMN_KEY, Model_Config::COLUMN_VALUE))->records(PDO::FETCH_OBJ);
      $recs = $cfgModel->mergedConfigValues()->columns(array(Model_Config::COLUMN_KEY, Model_Config::COLUMN_VALUE))->records(PDO::FETCH_OBJ);
      if ($recs == false) { // asi není vytvořena tabulka s globalním nastavením
         $cfgModel = new Model_Config();
         $recs = $cfgModel->columns(array(Model_Config::COLUMN_KEY, Model_Config::COLUMN_VALUE))->records(PDO::FETCH_OBJ);
         if ($recs == false) {
            throw new unexpectedValueException($this->tr("Nepodařilo se načíst konfiguraci. Chyba připojení k DB?"));
         }
      }

      if (isset($_REQUEST['FACEPREVIEW'])) {
         define('VVE_TEMPLATE_FACE', $_REQUEST['FACEPREVIEW']);
         define('CUBE_CMS_TEMPLATE_FACE', $_REQUEST['FACEPREVIEW']);
      }
      if (isset($_REQUEST['DEBUG'])) {
         define('VVE_DEBUG_LEVEL', $_REQUEST['DEBUG']);
         define('CUBE_CMS_DEBUG_LEVEL', $_REQUEST['DEBUG']);
      }

      foreach ($recs as $record) {
         if (!defined('CUBE_CMS_' . $record->{Model_Config::COLUMN_KEY}) && !defined('VVE_' . $record->{Model_Config::COLUMN_KEY})) {
            if ($record->{Model_Config::COLUMN_VALUE} == 'true') {
               define(strtoupper('VVE_' . $record->{Model_Config::COLUMN_KEY}), true);
               define(strtoupper('CUBE_CMS_' . $record->{Model_Config::COLUMN_KEY}), true);
            } else if ($record->{Model_Config::COLUMN_VALUE} == 'false') {
               define(strtoupper('VVE_' . $record->{Model_Config::COLUMN_KEY}), false);
               define(strtoupper('CUBE_CMS_' . $record->{Model_Config::COLUMN_KEY}), false);
            } else {
               define(strtoupper('VVE_' . $record->{Model_Config::COLUMN_KEY}), $record->{Model_Config::COLUMN_VALUE});
               define(strtoupper('CUBE_CMS_' . $record->{Model_Config::COLUMN_KEY}), $record->{Model_Config::COLUMN_VALUE});
            }
         }
      }
   }

   /**
    * Metoda provádí kontrolu verze jádra
    */
   private function checkCoreVersion()
   {
      /* Upgrade jádra */
      // upgrade
      if (defined('CUBE_CMS_VERSION') AND CUBE_CMS_VERSION != self::ENGINE_VERSION) { // kvůli neexistenci předchozí detekce
         $core = new Install_Core();
         $core->upgrade();
      } else if (!defined('CUBE_CMS_VERSION')) {
         $core = new Install_Core();
         $core->upgradeToMain();
      }
   }

   public static function shutDownHandler()
   {
      Loader::storeCache();
      // @todo dodělat vypisování chyb při špatném ukončení skriptu
   }

   /**
    * Metoda inicializuje objekty pro práci s hláškami
    */
   private function _initMessagesAndErrors()
   {
      if (CUBE_CMS_DEBUG_LEVEL > 0 || isset($_GET['DEBUG'])) {
         error_reporting(-1);
         ini_set('display_errors', 1);
      } else {
         error_reporting(E_ERROR | E_PARSE);
         ini_set('display_errors', 0);
      }
      ini_set("log_errors", 1);
      ini_set("error_log", AppCore::getAppWebDir() . "logs" . DIRECTORY_SEPARATOR . "php-error-" . date('Y-m-d') . ".log");
      set_error_handler(array('CoreErrors', 'errorHandler'));
      set_exception_handler(array('CoreErrors', 'exceptionHandler'));
      register_shutdown_function(array('AppCore', 'shutDownHandler'));
      //    Vytvoření objektu pro práci se zprávami
      self::$messages = new Messages('session', 'messages', true);
      self::$userErrors = new Messages('session', 'errors');

      // hlášky pro upgrade a update
      Install_Core::addUpgradeMessages();
   }

   /*
    * VEŘEJÉ METODY
    */

   /**
    * Metoda nastavuje hlavní adresář aplikace
    * @param string -- hlavní adresář aplikace
    */
   public static function setAppMainDir($appMainDir)
   {
      self::$_appWebDir = $appMainDir;
   }

   /**
    * Metoda nastavuje hlavní adresář s knihovnami aplikace
    * @param string -- hlavní adresář s knihovnami aplikace
    */
   public static function setAppMainLibDir($appMainLibDir)
   {
      self::$_appLibDir = $appMainLibDir;
   }

   /**
    * Metoda vytvoří hlavní menu aplikace
    */
   public function createMenus()
   {
      try {
         $menu = new Menu_Main();
         $menu->controller();
         $menu->view();
         $this->getCoreTpl()->menuObj = $menu->template();
      } catch (Exception $e) {
         new CoreErrors($e);
      }
      // inicializace admin menu
   }

   /**
    * Metoda vrací objekt core šablony. pokud nění vytvořena pokusí se ji vytvořit
    * @return Template_Core
    */
   public function getCoreTpl()
   {
      return Template_Core::getInstance();
   }

   /**
    * Metoda přiřadí do šablony hlavní proměnné systému
    */
   public function assignMainVarsToTemplate()
   {
      // Hlavni promene strany
      $this->getCoreTpl()->debug = CUBE_CMS_DEBUG_LEVEL;
      $this->getCoreTpl()->mainLangImagesPath = CUBE_CMS_IMAGES_LANGS_DIR . URL_SEPARATOR;
      $this->getCoreTpl()->categoryId = Category::getSelectedCategory()->getId();
      // Přiřazení jazykového pole
      $this->getCoreTpl()->setPVar("appLangsNames", Locales::getAppLangsNames());
      // Vytvoření odkazů s jazyky
      $langs = array();
      if (Locales::isMultilang()) {
         $link = new Url_Link(true);
         foreach (Locales::getAppLangsNames() as $langKey => $label) {
            $langArr = array(
               'name' => $langKey,
               'label' => $label,
            );
            if ($langKey != Locales::getDefaultLang()) {
               $langArr['link'] = (string) $link->clear(true)->lang($langKey);
            } else {
               $langArr['link'] = (string) $link->clear(true)->lang();
            }
            array_push($langs, $langArr);
         }
         unset($link);
         unset($langArr);
      }
      $this->getCoreTpl()->setPVar("appLangs", $langs);
      unset($langs);
      $this->getCoreTpl()->setPVar("appLang", Locales::getLang());
   }

   /**
    * metoda vyrenderuje šablonu
    * @todo Předělat, protože se nekontroluje existence jiných typů médií
    */
   public function renderTemplate()
   {
      //    načtení doby zpracovávání aplikace
      List ($usec, $sec) = Explode(' ', microtime());
      $endTime = ((float) $sec + (float) $usec);
      $this->getCoreTpl()->execTime = round($endTime - $this->_startTime, 4);
//       file_put_contents(AppCore::getAppCacheDir()."cache_test_enable.csv", $this->getCoreTpl()->execTime."\n", FILE_APPEND);
      $this->getCoreTpl()->countAllSqlQueries = Db_PDO::getCountQueries();
      $this->getCoreTpl()->addFile("tpl://" . Template_Core::getMainIndexTpl());
      echo($this->getCoreTpl());
   }

   /**
    * Metoda spouští moduly
    */
   public function runModule()
   {
      try {
         // načtení a kontrola cest u modulu
         $routesClassName = ucfirst(self::getCategory()->getModule()->getName()) . '_Routes';
         if (!class_exists($routesClassName)) {
            throw new UnexpectedPageException();
//            throw new BadClassException(sprintf($this->tr('Nepodařilo se načíst třídu "%s" cest (routes) modulu.'),
//            self::getCategory()->getModule()->getName()), 10);
         }
         //             Vytvoření objektu kontroleru
         $routes = new $routesClassName(self::getCategory());
         // kontola cest
         $routes->checkRoutes();
         if (!$routes->getActionName()
            OR ( $routes->getRouteName() == 'normal' AND ! self::$category->getRights()->isReadable())) {
            // redirect to login page
            $linkLogin = Url_Link::getCategoryLinkByModule('login');
            if (!Auth::isLogin() && $linkLogin && is_array($linkLogin)) {
               $linkLogin = reset($linkLogin);
               $linkLogin->param('redirect', Url_Request::getCurrentUrl())->redirect();
               flush();
               die;
            }
            AppCore::setErrorPage();
            return false;
         }
         // načtení kontroleru
         if ($routes->getClassName() == null) {
            $controllerClassName = ucfirst(self::getCategory()->getModule()->getName()) . '_Controller';
         } else {
            $controllerClassName = ucfirst($routes->getClassName()) . '_Controller';
         }
         if (!class_exists($controllerClassName)) {
            throw new BadClassException(sprintf($this->tr('Nepodařilo se načíst třídu "%s" controlleru modulu.'), self::getCategory()->getModule()->getName()), 10);
         }
         //             Vytvoření objektu kontroleru
         $controller = new $controllerClassName(self::getCategory(), $routes);
         unset($routes);
         $controller->runCtrl();
         // přiřazení šablony do výstupu
         $this->getCoreTpl()->module = $controller->_getTemplateObj();
         $this->getCoreTpl()->moduleAction = $controller->routes()->getActionName();
      } catch (ModuleException $e) {
         CoreErrors::addException($e);
      }
   }

   /**
    * Metoda spouští moduly staticky
    */
   public function runModuleStatic()
   {
      try {
         $cobj = new Object();
         $cobj->{Model_Category::COLUMN_NAME} = Url_Request::getInstance()->getName();
         $cobj->{Model_Category::COLUMN_MODULE} = Url_Request::getInstance()->getName();
         $cobj->{Model_Category::COLUMN_ALT} = null;

         $category = new Category_Core(null, true, $cobj);
         self::$category = $category;
         $moduleName = Url_Request::getInstance()->getName();
         $controllerClassName = ucfirst($moduleName) . '_Controller';
         $viewClassName = ucfirst($moduleName) . '_View';
         $controllerMethod = Url_Request::getInstance()->getAction() . 'Controller';
         $viewMethod = Url_Request::getInstance()->getAction() . 'View';

         if (!class_exists($controllerClassName) || !class_exists($viewClassName) || !method_exists($controllerClassName, $controllerMethod) || !method_exists($viewClassName, $viewMethod)) {
            AppCore::setErrorPage(true);
            return false;
         }

         // spuštění statické metody kontroleru a viewru
         $data = call_user_func(array($controllerClassName, $controllerMethod));
         $tpl = call_user_func(array($viewClassName, $viewMethod), $data);

         // přiřazení šablony do výstupu
         $this->getCoreTpl()->module = $tpl;
         $this->getCoreTpl()->moduleAction = Url_Request::getInstance()->getAction();
      } catch (Exception $e) {
         CoreErrors::addException($e);
      }
   }

   /**
    * Metoda spouští rss export na modulu
    */
   public function runModuleRss()
   {
      if (self::$category->haveFeed() == false) {
         AppCore::setErrorPage(true);
         return false;
      }

      if (!file_exists(AppCore::getAppLibDir() . self::MODULES_DIR . DIRECTORY_SEPARATOR
            . self::getCategory()->getModule()->getName() . DIRECTORY_SEPARATOR . 'rss.class.php')) {
         throw new BadClassException(sprintf($this->tr('Nepodařilo se načíst třídu pro zpracování rss zdroje modulu "%s"'), self::getCategory()->getModule()->getName()), 10);
      }

      // načtení a kontrola cest u modulu
      $routesClassName = ucfirst(self::getCategory()->getModule()->getName()) . '_Routes';
      if (!class_exists($routesClassName)) {
         throw new BadClassException(sprintf($this->tr('Nepodařilo se načíst třídu cest (routes) modulu "%s".'), self::getCategory()->getModule()->getName()), 10);
      }
      // Vytvoření objektu s cestama modulu
      $routes = new $routesClassName(self::getCategory());

      $rssClassName = ucfirst(self::getCategory()->getModule()->getName()) . '_Rss';
      $rssCore = new $rssClassName(self::getCategory(), $routes);

      $rssCore->runController();
      Template_Output::sendHeaders();
      $rssCore->runView();
   }

   /**
    * Metoda spustí samotný požadavek na modul, např generování listu v xml souboru
    */
   public function runModuleOnly()
   {
      if (Url_Request::getInstance()->getUrlType() == Url_Request::URL_TYPE_MODULE_REQUEST) {
         $ret = false;
         ob_start();
         // spuštění modulu
         try {
            if (!self::getCategory() instanceof Category_Core OR self::getCategory()->getCatDataObj() == null) {
               throw new CoreException(sprintf($this->tr('Špatně zadaný požadavek "%s" na modul'), Url_Request::getInstance()->getAction() . '.' . Url_Request::getInstance()->getOutputType()));
            }
            // načtení a kontrola cest u modulu
            $routesClassName = ucfirst(self::getCategory()->getModule()->getName()) . '_Routes';

            if (!class_exists($routesClassName)) {
               throw new BadClassException(sprintf($this->tr('Nepodařilo se načíst třídu cest (routes) modulu "%s".'), self::getCategory()->getModule()->getName()), 10);
            }
            // Vytvoření objektu s cestama modulu
            $routes = new $routesClassName();
//            $routes = new Routes();
            // kontola cest
            $routes->checkRoutes();
            if (!$routes->getActionName()) {
               AppCore::setErrorPage();
               return false;
               throw new BadRequestException($this->tr('Neplatná akce modulu'));
            }
            // načtení kontroleru
            $controllerClassName = ucfirst(self::getCategory()->getModule()->getName()) . '_Controller';
            if (!class_exists($controllerClassName)) {
               trigger_error(sprintf($this->tr('Nepodařilo se načíst třídu controlleru modulu "%s".'), self::getCategory()->getModule()->getName()), 10);
//               throw new BadClassException(sprintf($this->tr('Nepodařilo se načíst třídu "%s" controleru modulu.'), $controllerClassName));
            }
            //             Vytvoření objektu kontroleru
            $controller = new $controllerClassName(self::getCategory(), $routes);
            $ret = $controller->runCtrl();
         } catch (Exception $e) {
            new CoreErrors($e);
//            return false;
         }

          if ((Url_Request::getInstance()->isXHRRequest() AND $routes->getRespondClass() != null)
            OR ( Url_Request::getInstance()->getOutputType() == 'json' AND $routes->getRespondClass() == null AND ob_get_contents() == null )
                    /* je XHR, není implmentována metoda viewru a výstup je také prázdný */
            OR ( Url_Request::getInstance()->isXHRRequest() AND $controller->view()->hasViewer($controller->getActionView()) === false AND ob_get_contents() == null )
                 ) {
            // render odpovědi pro XHR
            if ($routes->getRespondClass() != null) {
               $class = $routes->getRespondClass();
               $respond = new $class();
            } else {
               $respond = new XHR_Respond_VVEAPI();
            }
            // přenos dat z šablony do dat odeslaných v respond
            $respond->setData($controller->_getTemplateObj()->getTemplateVars());
            $respond->renderRespond();
         } else if (in_array(Url_Request::getInstance()->getOutputType(), Template_Output::getHtmlTypes())) {
            /*
             * TODO -- tohle by se mělo dořešit, protože není jisté jestli tu má být vůbec render
             */
            // render normálního výpisu
            if (Template_Core::getMainIndexTpl() == Template_Core::INDEX_DEFAULT_TEMPLATE) {
               $controller->_getTemplateObj()->renderTemplate();
            } else {
               // render při změně indexu
               $this->getCoreTpl()->module = $controller->_getTemplateObj();
               $this->renderTemplate();
            }
         } else {
            // binary output
            Template_Output::sendHeaders();
            ob_flush();
         }
         ob_end_flush();
         return $ret;
      } else if (Url_Request::getInstance()->getUrlType() == Url_Request::URL_TYPE_MODULE_STATIC_REQUEST) {
         // načtení a kontrola cest u modulu
         $className = ucfirst(Url_Request::getInstance()->getName()) . '_Controller';
         $classNameV = ucfirst(Url_Request::getInstance()->getName()) . '_View';
         $methodName = Url_Request::getInstance()->getAction() . 'Controller';
         $methodNameV = Url_Request::getInstance()->getAction() . 'View';
         if (method_exists($className, $methodName)) {
            $result = call_user_func($className . "::" . $methodName);
            if ($result !== false AND method_exists($classNameV, $methodNameV)) {
               $result = call_user_func($classNameV . "::" . $methodNameV);
            }
            if ($result === false)
               AppCore::setErrorPage(true);
         } else {
            trigger_error(sprintf($this->tr('Neimplementovaná statická akce "%s" modulu'), $className . "::" . $methodName));
         }
      }
   }

   /**
    * Metoda inicializuje a spustí panel
    * @param string $side -- jaký panel je spuštěn (left, right, bottom, top, ...)
    * @todo dodělat implementaci ostatních pozic panelů
    */
   public function runPanels()
   {
      $panels = array();
      // vygenerování pole pro šablony panelů
      $panelPositions = Face::getParamStatic('panels');
      if (!empty($panelPositions)) {
         foreach ($panelPositions as $key => $pos) {
            $panels[$key] = array();
         }
      } else {
         $panelPositions = vve_parse_cfg_value(CUBE_CMS_PANEL_TYPES);
         foreach ($panelPositions as $pos) {
            $panels[$pos] = array();
         }
      }
      Template_Core::getInstance()->panels = $panels;
      /**
       *  @todo optimalizovat dotaz!! zabere 0.005 sekundy
       */
      $panelsM = new Model_Panel();
      $panelsM
         ->setSelectAllLangs(false)
         ->onlyWithAccess()
         ->order(array(Model_Panel::COLUMN_ORDER => Model_ORM::ORDER_DESC));
      // výběr jestli se zpracovávají individuální panely nebo globální
      if (self::$category->isIndividualPanels()) {
         $panelsM->where(" AND (" . Model_Panel::COLUMN_ID_SHOW_CAT . " = :idc OR " . Model_Panel::COLUMN_FORCE_GLOBAL . " = 1 )"
            . " AND " . Model_Category::COLUMN_MODULE . ' IS NOT NULL', array('idc' => self::$category->getId()), true);
      } else {
         $panelsM->where(" AND ( " . Model_Panel::COLUMN_ID_SHOW_CAT . " = 0 OR " . Model_Panel::COLUMN_FORCE_GLOBAL . " = 1 )"
            . " AND " . Model_Category::COLUMN_MODULE . ' IS NOT NULL', array(), true);
      }
      $panelsM->where(' OR '.Model_Panel::COLUMN_ADMIN_CAT." = 1", array(), true);

      $panels = $panelsM->records();

      foreach ($panels as $panel) {
         // pokud je panel vypnut přeskočíme zracování
         if (!isset(Template_Core::getInstance()->panels[(string) $panel->{Model_Panel::COLUMN_POSITION}])) {
            continue;
         }
         try {
            if($panel->{Model_Panel::COLUMN_ID_CAT} > Model_CategoryAdm::CATEGORY_BASE_ID) {
              $panelCat = new Category_Admin($panel->{Model_Panel::COLUMN_ID_CAT}, false);
            } else {
              $panelCat = new Category(null, false, $panel);
            }
            
            if (!$panelCat->getRights()->isReadable() || !file_exists(AppCore::getAppLibDir() . self::MODULES_DIR . DIRECTORY_SEPARATOR
                  . $panelCat->getModule()->getName() . DIRECTORY_SEPARATOR . 'panel.class.php')) {
               continue;
            }
            // načtení a kontrola cest u modulu
            $routesClassName = ucfirst($panelCat->getModule()->getName()) . '_Routes';
            if (!class_exists($routesClassName)) {
               throw new BadClassException(sprintf($this->tr('Nepodařilo se načíst třídu "%s" cest (routes) modulu.'), $panelCat->getModule()->getName()), 10);
            }
            //             Vytvoření objektu kontroleru
            $routes = new $routesClassName(null, $panelCat);
            $controllerClassName = ucfirst($panelCat->getModule()->getName()) . '_Panel';
            if (!class_exists($controllerClassName)) {
               throw new BadClassException(sprintf($this->tr('Nepodařilo se načíst třídu "%s" controlleru panelu modulu.'), self::getCategory()->getModule()->getName()), 10);
            }

            // Vytvoření objektu kontroleru
            $panelController = new $controllerClassName($panelCat, $routes);
            $panelController->run();
            array_push(Template_Core::getInstance()->panels[(string) $panel->{Model_Panel::COLUMN_POSITION}], $panelController->_getTemplateObj());
         } catch (Exception $exc) {
            CoreErrors::addException($exc);
         }
      }
   }

   /**
    * Metoda přiřadí chyby do šablony
    */
   public function assignCoreErrorsToTpl()
   {
      Template_Core::getInstance()->coreErrors = CoreErrors::getErrors();
      Template_Core::getInstance()->coreErrorsEmpty = CoreErrors::isEmpty();
   }

   /**
    * Metoda přiřadí zprávy šablonovacího systému
    */
   public function assignMessagesToTpl()
   {
      $this->getCoreTpl()->messages = self::getInfoMessages()->getMessages();
      $this->getCoreTpl()->moduleErrors = self::getUserErrors()->getMessages();
      // výmaz uložených zpráv (kvůli requestů je tady)
      self::getInfoMessages()->eraseSavedMessages();
      self::getUserErrors()->eraseSavedMessages();
   }

   /**
    * Metoda spouští kód pro generování specielních stránek
    */
   public function runCoreModule()
   {
      $module = 'Module_' . ucfirst(self::$category->getDataObj()->{Model_Category::COLUMN_MODULE});
      // forbiden access ?
      if (!self::$category->getRights()->isReadable() && !self::$category->getRights()->isWritable() && !self::$category->getRights()->isControll()) {
         $module = 'Module_ForbiddenAccess';
      }
      $className = $module;
      if (class_exists($className)) {
         $ctrl = new $className(self::$category);
      } else {
         $ctrl = new Module_ErrPage(self::$category);
      }
      $ctrl->runController();
      // view metoda
      $viewM = 'run' . ucfirst(Url_Request::getInstance()->getOutputType()) . 'View';
      if (method_exists($ctrl, $viewM) AND Url_Request::getInstance()->getOutputType() != null AND Url_Request::getInstance()->getOutputType() != 'html') {
         Template_Output::sendHeaders();
         $ctrl->{$viewM}();
      } else {
         $ctrl->runView();
         if (!Url_Request::getInstance()->isXHRRequest()) {
            $this->getCoreTpl()->module = $ctrl->template();
         } else {
            $ctrl->template()->renderTemplate();
         }
      }
   }

   /**
    * Metoda spustí akci nad JsPluginem
    */
   public function runJsPlugin()
   {
      try {
         $pluginName = 'JsPlugin_' . ucfirst(Url_Request::getInstance()->getName());
         if (class_exists($pluginName)) {
            $jsPlugin = new $pluginName();
         } else if (class_exists(ucfirst(Url_Request::getInstance()->getName()))) {
            $pluginName = ucfirst(Url_Request::getInstance()->getName());
            $jsPlugin = new $pluginName();
         } else {
            throw new UnexpectedValueException($this->tr('Neexistující JsPlugin'));
         }
         // vytvoření souboru
         Template_Output::sendHeaders();
         $jsPlugin->runAction(Url_Request::getInstance()->getAction(), Url_Request::getInstance()->getUrlParams(), Url_Request::getInstance()->getOutputType());
      } catch (Exception $exc) {
         echo $exc->getMessage() . "\n";
         if (CUBE_CMS_DEBUG_LEVEL > 0) {
            echo $exc->getTraceAsString();
         }
      }
      exit();
   }

   /**
    * Metoda pro spuštění akce na componentě
    */
   public function runComponent()
   {
      $componentName = 'Component_' . ucfirst(Url_Request::getInstance()->getName());
      $component = new $componentName();
      // z komponenty patří výstup zde
      $component->runAction(Url_Request::getInstance()->getAction(), Url_Request::getInstance()->getUrlParams(), Url_Request::getInstance()->getOutputType());
   }

   /**
    * Metoda pro spuštění akce na componentě
    */
   public function runProcessModule()
   {
      if (isset($_GET[Url_Request::URL_TYPE_MODULE_PROCESS]) && isset($_GET[Url_Request::URL_TYPE_MODULE_PROCESS_DO]) && class_exists($_GET[Url_Request::URL_TYPE_MODULE_PROCESS] . '_Controller') && method_exists($_GET[Url_Request::URL_TYPE_MODULE_PROCESS] . '_Controller', $_GET[Url_Request::URL_TYPE_MODULE_PROCESS_DO])) {
         try {
            if (Url_Request::isXHRRequest()) {
               AppCore::getInfoMessages()->changeSaveStatus(false);
            }
            $payload = call_user_func(array($_GET[Url_Request::URL_TYPE_MODULE_PROCESS] . '_Controller', $_GET[Url_Request::URL_TYPE_MODULE_PROCESS_DO]));
            // pokud je XHR request, tak ukončíme běh a vrátíme XHR API odpověď s chybama a podobně. Asi by bylo dobré vracet payload
//            if (Url_Request::isXHRRequest()) {
//               $response = new XHR_Respond_VVEAPI();
//               $response->setData($payload);
//               $response->renderRespond();
//               die;
//            }
         } catch (Exception $exc) {
            CoreErrors::addException($exc);
         }
      }
   }

   /**
    * Metoda načte soubor se specialními vlastnostmi přenesenými do šablony,
    * které jsou jednotné pro celý web
    * @todo předělat pro načítání z adresáře Webu, ne knihoven
    */
   private function initialWebSettings()
   {
      $fileName = 'initial' . ucfirst(Url_Request::getInstance()->getOutputType()) . '.php';
      if (file_exists(AppCore::getAppWebDir() . self::ENGINE_CONFIG_DIR . DIRECTORY_SEPARATOR . $fileName)) {
         require AppCore::getAppWebDir() . self::ENGINE_CONFIG_DIR . DIRECTORY_SEPARATOR . $fileName;
      }
   }

   private function checkAttacks()
   {
      // přidat rychlou deblokaci
      if (defined('CUBE_CMS_VERSION') && version_compare(CUBE_CMS_VERSION, '8.0.0' ,'>') && Model_IPBlocks::isBlocked(Utils_Net::getClientIP())) {
         echo 'Yout IP address is blocekd. Please write email to: ' . CUBE_CMS_WEB_MASTER_EMAIL;
         if (function_exists('http_response_code')) {
            http_response_code(503);
         }
         die;
      }
   }

   /**
    * Hlavní metoda provádění aplikace
    */
   public function runCore()
   {
      // načtení systémového konfiguračního souboru
      try {
         // inicializace db konektoru
         $this->_initDb();
         // config
         $this->_initConfig();
         // kontrola útoků a blokací
         $this->checkAttacks();
         // init jazyka
         Locales::factory();
         // kontrola verze enginu
         $this->checkCoreVersion();
         // inicializace URL
         Url_Request::factory();
         //    inicializace sessions
         Session::factory();
         //    Inicializace chybových hlášek
         $this->_initMessagesAndErrors();

         //inicializace lokalizace
//         Locales::factory();
         Url_Request::detectLang();
         // tohle je tady kvůli základní inicializaci překladače
         $tr = new Translator();
         // provedení autorizace
         Auth::authenticate();
         $adm = Menu_Admin::getInstance();
         // parsování requestu
         Url_Request::getInstance();
         // výběr jazyka a locales
//         Locales::setLang(Url_Request::getInstance()->getUrlLang());
         Locales::selectLang();
         // inicializace Šablonovacího systému
         Template::factory();
      } catch (Exception $exc) {
         if (strtolower($_SERVER['SERVER_NAME']) == 'localhost' OR strtolower($_SERVER['SERVER_NAME']) == '127.0.0.1') {
            echo $exc->getTraceAsString();
         } else {
            echo nl2br($exc->getMessage());
         }
         die();
      }

      if (CUBE_CMS_DEBUG_LEVEL >= 3 AND function_exists('xdebug_start_trace')) {
         xdebug_start_trace(AppCore::getAppCacheDir() . 'trace.log');
      }

      // zapnutí buferu podle výstupu
      Template_Output::factory(Url_Request::getInstance()->getOutputType());
      if (Url_Request::getInstance()->getUrlLang() != null AND Locales::isLang(Url_Request::getInstance()->getUrlLang())) {
         $reqUrl = str_replace(Url_Request::getInstance()->getUrlLang() . URL_SEPARATOR, null, Url_Request::getInstance()->getRequestUrl());
      } else {
         $reqUrl = Url_Request::getInstance()->getRequestUrl();
      }
      // odstranění parametrů a kotev kvůli testům
      $reqUrl = preg_replace(array('/\?.*/', '/#.*/'), array('', ''), $reqUrl);
      $catUrl = Url_Request::getInstance()->getCategory();

      // není předána kategortie
      if ($catUrl == null && $reqUrl == null) {
         // bude vybrána výchozí kategorie
         AppCore::setErrorPage(false);
      }

      $className = 'Module_' . ucfirst(Url_Request::getInstance()->getCategory()) . '_Category';
      // načtení kategorie
      if (Url_Request::getInstance()->getUrlType() == Url_Request::URL_TYPE_CORE_MODULE AND class_exists($className)) { // Core Module
         self::$category = new $className(Url_Request::getInstance()->getCategory(), true);
      } else if (( ($reqUrl == '' AND $catUrl == null) OR ( $reqUrl != '' AND $catUrl != null))) {
         if (!Url_Request::getInstance()->isAdminCategory() && (int) Url_Request::getInstance()->getCategory() < 32798) {
            self::$category = new Category(Url_Request::getInstance()->getCategory(), true);
         } else {
            self::$category = new Category_Admin(Url_Request::getInstance()->getCategory(), true);
         }
         Url_Link::setCategory(Url_Request::getInstance()->getCategoryUrlKey(self::$category->getUrlKey(), self::$category->getId()));
      }

      if (Auth::isAdmin() && self::$category && !self::$category->isValid() && ctype_digit(Url_Request::getInstance()->getCategory())) {
         self::$category = new Category_Admin(Url_Request::getInstance()->getCategory(), true);
      }

      if (((self::$category instanceof Category_Core == false) OR ! self::$category->isValid())
         AND Url_Request::getInstance()->getUrlType() != Url_Request::URL_TYPE_MODULE_STATIC_REQUEST
      /* OR !self::$category->getRights()->isReadable() */) { // Chyba stránky
         self::$category = new Module_ErrPage_Category(Url_Request::getInstance()->getCategory(), true);
         Url_Link::setCategory(self::$category->getUrlKey());
         AppCore::setErrorPage(true);
      }
      unset($className);
      
      /* Proces na modulu */
      $this->runProcessModule();
      
      if (!Url_Request::getInstance()->isFullPage()) {
         // vynulování chyby, protože chybová stránka je výchozí stránka
         AppCore::setErrorPage(false);
         // je zpracovává pouze požadavek na část aplikace
         // vybrání části pro zpracování podle požadavku
         switch (Url_Request::getInstance()->getUrlType()) {
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
         if ($ret === false) {
            AppCore::setErrorPage(true);
         }
         if (Url_Request::getInstance()->getUrlType() != Url_Request::URL_TYPE_MODULE_RSS) {
            if (AppCore::isErrorPage()) {
               trigger_error($this->tr("Neplatný požadavek na aplikaci"), E_USER_ERROR);
            }
            // render chyb
            if (!CoreErrors::isEmpty()) {
               CoreErrors::printErrors();
            }
         }
      }
      if (Url_Request::getInstance()->isFullPage()
         OR ( AppCore::isErrorPage() AND Url_Request::getInstance()->getUrlType() == Url_Request::URL_TYPE_MODULE_RSS)) {
         // Globální inicializace proměných do šablony
         $this->initialWebSettings();


         try {
            if (Url_Request::getInstance()->getUrlType() == Url_Request::URL_TYPE_NORMAL AND ! AppCore::isErrorPage()) {
               // zpracovávní modulu
               $this->runModule();
            } else if (Url_Request::getInstance()->getUrlType() == Url_Request::URL_TYPE_MODULE_STATIC_REQUEST AND ! AppCore::isErrorPage()) {
               // zpracovávní modulu ve static
               $this->runModuleStatic(true);
            }

            if (Url_Request::getInstance()->getUrlType() == Url_Request::URL_TYPE_CORE_MODULE
               OR AppCore::isErrorPage()) {
               // zpracování stránky enginu (sitemap, rss, error, atd.)
               $this->runCoreModule();
            }
         } catch (UnexpectedPageException $e) {
            AppCore::setErrorPage(true);
            /* @TODO tohle asij do háje zeleného */
            if ($e->getMessage() != null) {
               AppCore::getUserErrors()->addMessage($e->getMessage());
            }
            $this->runCoreModule();
         } catch (UnauthorizedAccessException $e) {
            self::$category = new Module_UnauthorizedAccess_Category();
            /* @TODO tohle asij do háje zeleného */
            if ($e->getMessage() != null) {
               AppCore::getUserErrors()->addMessage($e->getMessage());
            }
            $this->runCoreModule();
         } catch (ForbiddenAccessException $e) {
            self::$category = new Module_ForbiddenAccess_Category();
            /* @TODO tohle asij do háje zeleného */
            if ($e->getMessage() != null) {
               AppCore::getUserErrors()->addMessage($e->getMessage());
            }
            $this->runCoreModule();
         } catch (Exception $e) {
            CoreErrors::addException($e);
         }

         //vytvoření hlavního menu
         $this->createMenus();
         try {
            // =========   spuštění panelů
            $this->runPanels();
         } catch (Exception $exc) {
            CoreErrors::addException($exc);
         }

         // Přiřazení hlášek do šablony
         $this->assignMessagesToTpl();
         // vložení proměných šablony z jadra
         $this->assignMainVarsToTemplate();
         // přiřazení chybových hlášek jádra do šablony
         $this->assignCoreErrorsToTpl();
         // render šablony
         $this->renderTemplate();
      }
      if (CUBE_CMS_DEBUG_LEVEL >= 3 AND function_exists('xdebug_stop_trace')) {
         xdebug_stop_trace();
      }
      return true;
   }

}

?>
