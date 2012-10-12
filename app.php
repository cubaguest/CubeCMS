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
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'trobject.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'debug'.DIRECTORY_SEPARATOR.'debug.class.php';
class AppCore extends TrObject {
   /**
    * Název enginu
    */
   const ENGINE_NAME = 'Cube CMS';

   /**
    * Verze enginu
    */
   const ENGINE_VERSION = 7;

   /**
    * Revize Enginu
    * @internal before change release move new config values from _initConfig method
    */
   const ENGINE_RELEASE = 16;

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
   private function __construct()
   {
      //		inicializace stratovacího času
      List ($usec, $sec) = Explode (' ', microtime());
      $this->_startTime=((float)$sec + (float)$usec);
      //		Definice globálních konstant
      define('URL_SEPARATOR', '/');
      // verze PHP
      if(!defined('PHP_VERSION_ID')){
         $version = explode('.',PHP_VERSION);
         define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
         define('PHP_MAJOR_VERSION',   $version[0]);
         define('PHP_MINOR_VERSION',   $version[1]);
         define('PHP_RELEASE_VERSION', $version[2]);
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
      if(strtoupper (substr(PHP_OS, 0,3)) != 'WIN' ) {
         define('SERVER_PLATFORM', 'UNIX');
      } else {
         define('SERVER_PLATFORM', 'WIN');
      }
      // inicializace parametrů jádra a php
      $this->_initCore();

      //	přidání adresáře pro načítání knihoven
      set_include_path(AppCore::getAppLibDir().self::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR . PATH_SEPARATOR . get_include_path());

      // Autoloaders
      spl_autoload_extensions('.class.php,.php');
      // spl_autoload_register(); PHP 5.3.10 make warnings here
      spl_autoload_register(array('AppCore', 'libLoader'));
      spl_autoload_register(array('AppCore', 'moduleLoader'));
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
         throw new BadMethodCallException($this->tr('Objekt aplikace byl již vytvořen'));
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
      return self::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR;
   }

   /**
    * Metoda vrací cestu k cache adresáři aplikace
    * @return string
    */
   public static function getAppCacheDir()
   {
      return self::getAppWebDir().self::ENGINE_CACHE_DIR.DIRECTORY_SEPARATOR;
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
      if($var){
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
    */
   public static function getUrlRequest()
   {
      return self::$urlRequest;
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
      // base classes
      $this->_initBaseClasses();

      date_default_timezone_set('Europe/Prague');
      // nastavení mb kodování na UTF protože celá aplikace pracuje s UTF
      mb_internal_encoding("UTF-8");
      iconv_set_encoding('input_encoding', 'UTF-8');
      iconv_set_encoding('output_encoding', 'UTF-8');
      iconv_set_encoding('internal_encoding', 'UTF-8');
      ini_set("default_charset", "utf-8");
      // max upload Limit
      $max_upload = vve_parse_size(ini_get('upload_max_filesize'));
      $max_post = vve_parse_size(ini_get('post_max_size'));
      $memory_limit = vve_parse_size(ini_get('memory_limit'));
      define('VVE_MAX_UPLOAD_SIZE', min($max_upload, $max_post, $memory_limit));
   }

   /**
    * Metoda načte základní třídy
    */
   private function _initBaseClasses()
   {
      // exceptions
      $exFiles = array('coreException','dbException','badClassException','badFileException',
         'imageException','badRequestException', 'controllerException', 'UnexpectedPageException', 'ModuleException');
      foreach ($exFiles as $exFile) {
         require_once (AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR . DIRECTORY_SEPARATOR
            . AppCore::ENGINE_EXCEPTIONS_DIR . DIRECTORY_SEPARATOR . $exFile.'.class.php');
      }
      // logování
      require_once (AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'log.class.php'); // loger
      // soubor s globálními funkcemi, které nejsou součástí php, ale časem by mohly být
      require_once (AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR . DIRECTORY_SEPARATOR
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
      if($recs == false){ // asi není vytvořena tabulka s globalním nastavením
         $cfgModel = new Model_Config();
         $recs = $cfgModel->columns(array(Model_Config::COLUMN_KEY, Model_Config::COLUMN_VALUE))->records(PDO::FETCH_OBJ);
         if($recs == false){
            throw new unexpectedValueException($this->tr("Nepodařilo se načíst konfiguraci. Chyba připojení k DB?"));
         }
      }
      foreach ($recs as $record) {
         if(!defined('VVE_'.$record->{Model_Config::COLUMN_KEY})) {
            if($record->{Model_Config::COLUMN_VALUE} == 'true') {
               define(strtoupper('VVE_'.$record->{Model_Config::COLUMN_KEY}), true);
            } else if($record->{Model_Config::COLUMN_VALUE} == 'false') {
               define(strtoupper('VVE_'.$record->{Model_Config::COLUMN_KEY}), false);
            } else {
               define(strtoupper('VVE_'.$record->{Model_Config::COLUMN_KEY}), $record->{Model_Config::COLUMN_VALUE});
            }
         }
      }
      
      // THIS IS FOR UPDATE - move this configs after release is ready to update sql script
      define('VVE_DEFAULT_LANG_SUBSTITUTION', true);
      define('VVE_ANALYTICS_DISABLED_HOSTS', '127.0.0.1');
      define('VVE_ENABLE_LANG_AUTODETECTION', false);
   }

   /**
    * Metoda provádí kontrolu verze jádra
    */
   private function checkCoreVersion()
   {
      /* Upgrade jádra */
      // upgrade
      if(defined('VVE_VERSION') AND VVE_VERSION != self::ENGINE_VERSION){ // kvůli neexistenci předchozí detekce
         $core = new Install_Core();
         $core->upgrade();
      } else if(!defined('VVE_VERSION')) {
         $core = new Install_Core();
         $core->upgradeToMain();
      } else {
      }
      // update
      if(!defined('VVE_RELEASE')){
         define('VVE_RELEASE', 0);
      }
      if(VVE_RELEASE != self::ENGINE_RELEASE){ // kvůli neexistenci předchozí detekce
         $core = new Install_Core();
         $core->update();
      }
   }

   /**
    * Metoda pro automatické načtení knihoven
    * @todo refaktoring nutný
    */
   public static function libLoader($classOrigName)
   {
      $file = strtolower($classOrigName) . '.class.php';
      $classL = strtolower($classOrigName);
      $pathDirs = explode('_', $classL);
      $pathFull = implode('/', $pathDirs);
      array_pop($pathDirs); // remove last path item
      $pathShort = implode('/', $pathDirs);
      // short path
      if (is_file(AppCore::getAppLibDir() . AppCore::ENGINE_LIB_DIR
            . DIRECTORY_SEPARATOR . $pathShort . DIRECTORY_SEPARATOR . $file)) {
         require_once AppCore::getAppLibDir() . AppCore::ENGINE_LIB_DIR
            . DIRECTORY_SEPARATOR . $pathShort . DIRECTORY_SEPARATOR . $file;
         return true;
      }
      // full path
      if (is_file(AppCore::getAppLibDir() . AppCore::ENGINE_LIB_DIR
            . DIRECTORY_SEPARATOR . $pathFull . DIRECTORY_SEPARATOR . $file)) {
         require_once AppCore::getAppLibDir() . AppCore::ENGINE_LIB_DIR
            . DIRECTORY_SEPARATOR . $pathFull . DIRECTORY_SEPARATOR . $file;
         return true;
      }
      return false;
   }
   
   /**
    * Metoda pro automatické načtení knihoven
    * @todo refaktoring nutný
    */
   public static function moduleLoader($classOrigName)
   {
      $file = strtolower($classOrigName) . '.class.php';
      $classL = strtolower($classOrigName);
      $pathDirs = explode('_', $classL);
      $moduleFile = end($pathDirs) . '.class.php';
      $pathFull = implode('/', $pathDirs);
      array_pop($pathDirs);
      $pathShort = implode('/', $pathDirs);
      
      // short path
      if (file_exists(AppCore::getAppLibDir() . AppCore::MODULES_DIR
            . DIRECTORY_SEPARATOR . $pathShort . DIRECTORY_SEPARATOR . $moduleFile)) {
         require_once AppCore::getAppLibDir() . AppCore::MODULES_DIR
            . DIRECTORY_SEPARATOR . $pathShort . DIRECTORY_SEPARATOR . $moduleFile;
         return true;
      } 
      // full path
      if (file_exists(AppCore::getAppLibDir() . AppCore::MODULES_DIR
            . DIRECTORY_SEPARATOR . $pathFull . DIRECTORY_SEPARATOR . $moduleFile)) {
         require_once AppCore::getAppLibDir() . AppCore::MODULES_DIR
            . DIRECTORY_SEPARATOR . $pathFull . DIRECTORY_SEPARATOR . $moduleFile;
         return true;
      } 
      return false;
   }

   public static function shutDownHandler() 
   {
      // @todo dodělat vypisování chyb při špatném ukončení skriptu
   }
   /**
    * Metoda inicializuje objekty pro práci s hláškami
    */
   private function _initMessagesAndErrors()
   {
      if(VVE_DEBUG_LEVEL > 0){
         error_reporting(-1);
         ini_set('display_errors', 1);
      } else {
         error_reporting(E_ERROR|E_WARNING|E_PARSE);
         ini_set('display_errors', 0);
      }
      set_error_handler(array('CoreErrors', 'errorHandler') );
      set_exception_handler( array( 'CoreErrors', 'exceptionHandler' ) );
      register_shutdown_function( array( 'AppCore', 'shutDownHandler' ) );
      //		Vytvoření objektu pro práci se zprávami
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
      } catch (Exception $e ) {
         new CoreErrors($e);
      }
      // inicializace admin menu
      if(Auth::isLogin() && Auth::isAdmin()){
         try {
            $menu = new Menu_Admin();
            $menu->controller();
            $menu->view();
            $this->getCoreTpl()->menuAdminObj = $menu->template();
         } catch (Exception $e) {
            new CoreErrors($e);
         }
      }
   }

   /**
    * Metoda vrací objekt core šablony. pokud nění vytvořena pokusí se ji vytvořit
    * @return Template_Core
    */
   public function getCoreTpl()
   {
      if(!($this->coreTpl instanceof Template_Core)){
         $this->coreTpl = new Template_Core();
      }
      return $this->coreTpl;
   }

   /**
    * Metoda přiřadí do šablony hlavní proměnné systému
    */
   public function assignMainVarsToTemplate()
   {
      //	Hlavni promene strany
      $this->getCoreTpl()->debug = VVE_DEBUG_LEVEL;
      $this->getCoreTpl()->mainLangImagesPath = VVE_IMAGES_LANGS_DIR.URL_SEPARATOR;
      $this->getCoreTpl()->categoryId = Category::getSelectedCategory()->getId();
      // Přiřazení jazykového pole
      $this->getCoreTpl()->setPVar("appLangsNames", Locales::getAppLangsNames());
      // Vytvoření odkazů s jazyky
      $langs = array();
      if(Locales::isMultilang()){
         $link = new Url_Link(true);
         foreach (Locales::getAppLangsNames() as $langKey => $label) {
            $langArr = array(
               'name' => $langKey,
               'label' => $label,
            );
            if($langKey != Locales::getDefaultLang()) {
               $langArr['link'] = (string)$link->clear(true)->lang($langKey);
            } else {
               $langArr['link'] = (string)$link->clear(true)->lang();
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
      //		načtení doby zpracovávání aplikace
      List ($usec, $sec) = Explode(' ', microtime());
      $endTime = ((float) $sec + (float) $usec);
      $this->getCoreTpl()->execTime = round($endTime - $this->_startTime, 4);
//       file_put_contents(AppCore::getAppCacheDir()."cache_test_enable.csv", $this->getCoreTpl()->execTime."\n", FILE_APPEND);
      $this->getCoreTpl()->countAllSqlQueries = Db_PDO::getCountQueries();
      $this->getCoreTpl()->addTplFile(Template_Core::getMainIndexTpl(), true);
      echo($this->getCoreTpl());
   }

   /**
    * Metoda spouští moduly
    */
   public function runModule()
   {
      try {
         // načtení a kontrola cest u modulu
         $routesClassName = ucfirst(self::getCategory()->getModule()->getName()).'_Routes';
         if(!class_exists($routesClassName)) {
            throw new BadClassException(sprintf($this->tr('Nepodařilo se načíst třídu "%s" cest (routes) modulu.'),
            self::getCategory()->getModule()->getName()), 10);
         }
         //					Vytvoření objektu kontroleru
         $routes = new $routesClassName(self::$urlRequest->getModuleUrlPart(), self::getCategory());
         /**
          * @param Routes
          */
//         $routes = new Routes($urlRequest);
         // kontola cest
         $routes->checkRoutes();
         if(!$routes->getActionName() 
            OR ($routes->getRouteName() == 'normal' AND !self::$category->getRights()->isReadable()) ) {
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
            throw new BadClassException(sprintf($this->tr('Nepodařilo se načíst třídu "%s" controlleru modulu.'),
            self::getCategory()->getModule()->getName()), 10);
         }
         //					Vytvoření objektu kontroleru
         $controller = new $controllerClassName(self::getCategory(), $routes);
         unset ($routes);
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
         $cobj->{Model_Category::COLUMN_NAME} = self::$urlRequest->getName();
         $cobj->{Model_Category::COLUMN_MODULE} = self::$urlRequest->getName();
         $cobj->{Model_Category::COLUMN_ALT} = null;
         
         $category = new Category_Core(null, true, $cobj);
         self::$category = $category;
         $moduleName = self::$urlRequest->getName();
         $controllerClassName = ucfirst($moduleName).'_Controller';
         $viewClassName = ucfirst($moduleName).'_View';
         $controllerMethod = self::$urlRequest->getAction().'Controller';
         $viewMethod = self::$urlRequest->getAction().'View';
         
         if( !class_exists($controllerClassName) 
               || !class_exists($viewClassName) 
               || !method_exists($controllerClassName, $controllerMethod)
               || !method_exists($viewClassName, $viewMethod)){
            AppCore::setErrorPage(true);
            return false;
         }
         
         //	spuštění statické metody kontroleru a viewru
         $data = call_user_func(array($controllerClassName, $controllerMethod));
         $tpl = call_user_func(array($viewClassName, $viewMethod), $data);
         
         // přiřazení šablony do výstupu
         $this->getCoreTpl()->module = $tpl;
         $this->getCoreTpl()->moduleAction = self::$urlRequest->getAction();
      } catch (Exception $e) {
         CoreErrors::addException($e);
      }
   }

   /**
    * Metoda spouští rss export na modulu
    */
   public function runModuleRss()
   {
      if(self::$category->haveFeed() == false){
         AppCore::setErrorPage(true);
         return false;
      }

      if(!file_exists(AppCore::getAppLibDir().self::MODULES_DIR.DIRECTORY_SEPARATOR
         .self::getCategory()->getModule()->getName().DIRECTORY_SEPARATOR.'rss.class.php')){
         throw new BadClassException(sprintf($this->tr('Nepodařilo se načíst třídu pro zpracování rss zdroje modulu "%s"'),
         self::getCategory()->getModule()->getName()), 10);
      }

      // načtení a kontrola cest u modulu
      $routesClassName = ucfirst(self::getCategory()->getModule()->getName()).'_Routes';
      if(!class_exists($routesClassName)) {
         throw new BadClassException(sprintf($this->tr('Nepodařilo se načíst třídu cest (routes) modulu "%s".'),
         self::getCategory()->getModule()->getName()), 10);
      }
      //	Vytvoření objektu s cestama modulu
      $routes = new $routesClassName(self::$urlRequest->getModuleUrlPart(),self::getCategory());

      $rssClassName = ucfirst(self::getCategory()->getModule()->getName()).'_Rss';
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
      if(self::$urlRequest->getUrlType() == Url_Request::URL_TYPE_MODULE_REQUEST) {
         $ret = false;
         ob_start();
         // spuštění modulu
         try {
            if(!self::getCategory() instanceof Category_Core OR self::getCategory()->getCatDataObj() == null) {
               throw new CoreException(sprintf($this->tr('Špatně zadaný požadavek "%s" na modul'),
               self::$urlRequest->getAction().'.'.self::$urlRequest->getOutputType()));
            }
            // načtení a kontrola cest u modulu
            $routesClassName = ucfirst(self::getCategory()->getModule()->getName()).'_Routes';
            
            if(!class_exists($routesClassName)) {
               throw new BadClassException(sprintf($this->tr('Nepodařilo se načíst třídu cest (routes) modulu "%s".'),
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
               throw new BadRequestException($this->tr('Neplatná akce modulu'));
            }
            // načtení kontroleru
            $controllerClassName = ucfirst(self::getCategory()->getModule()->getName()).'_Controller';
            if(!class_exists($controllerClassName)) {
               trigger_error(sprintf($this->tr('Nepodařilo se načíst třídu controlleru modulu "%s".'),
                       self::getCategory()->getModule()->getName()), 10);
//               throw new BadClassException(sprintf($this->tr('Nepodařilo se načíst třídu "%s" controleru modulu.'), $controllerClassName));
            }
            //					Vytvoření objektu kontroleru
            $controller = new $controllerClassName(self::getCategory(), $routes);
            $ret =  $controller->runCtrl();
         } catch (Exception $e ) {
            new CoreErrors($e);
//            return false;
         }
         
         if((AppCore::getUrlRequest()->isXHRRequest() AND $routes->getRespondClass() != null)
            OR (self::$urlRequest->getOutputType() == 'json' AND $routes->getRespondClass() == null AND ob_get_contents() == null )){
            // render odpovědi pro XHR
            if($routes->getRespondClass() != null){
               $class = $routes->getRespondClass();
               $respond = new $class();
            } else {
               $respond = new XHR_Respond_VVEAPI();
            }
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
            trigger_error(sprintf($this->tr('Neimplementovaná statická akce "%s" modulu'),$className."::".$methodName));
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
      $this->coreTpl->panels = array();
      // vygenerování pole pro šablony panelů
      $panelPositions = vve_parse_cfg_value(VVE_PANEL_TYPES);
      foreach ($panelPositions as $panel) {
         $this->coreTpl->panels[$panel] = array();
      }
      /**
       *  @todo optimalizovat dotaz!! zabere 0.005 sekundy
       */
      $panelsM = new Model_Panel();
      $panelsM
         ->setSelectAllLangs(false)
         ->onlyWithAccess()
         ->order(array(Model_Panel::COLUMN_ORDER => Model_ORM::ORDER_DESC));
      // výběr jestli se zpracovávají individuální panely nebo globální
      if(self::$category->isIndividualPanels()) {
         $panelsM->where(" AND (".Model_Panel::COLUMN_ID_SHOW_CAT." = :idc OR ".Model_Panel::COLUMN_FORCE_GLOBAL." = 1 )"
               ." AND ".Model_Category::COLUMN_MODULE.' IS NOT NULL', 
               array('idc' => self::$category->getId()), true);
      } else {
         $panelsM->where(" AND ( ".Model_Panel::COLUMN_ID_SHOW_CAT." = 0 OR ".Model_Panel::COLUMN_FORCE_GLOBAL." = 1 )"
               ." AND ".Model_Category::COLUMN_MODULE.' IS NOT NULL', 
               array(), true);
      }
      $panels = $panelsM->records();

      foreach ($panels as $panel) {
         // pokud je panel vypnut přeskočíme zracování
         if(!isset ($this->coreTpl->panels[(string) $panel->{Model_Panel::COLUMN_POSITION}])){
            continue;
         }
         try {
            $panelCat = new Category(null, false, $panel);
            
            if (!$panelCat->getRights()->isReadable() 
               || !file_exists(AppCore::getAppLibDir() . self::MODULES_DIR . DIRECTORY_SEPARATOR
                  . $panelCat->getModule()->getName() . DIRECTORY_SEPARATOR . 'panel.class.php')) {
               continue;
            }
            // načtení a kontrola cest u modulu
            $routesClassName = ucfirst($panelCat->getModule()->getName()) . '_Routes';
            if (!class_exists($routesClassName)) {
               throw new BadClassException(sprintf($this->tr('Nepodařilo se načíst třídu "%s" cest (routes) modulu.'),
                     $panelCat->getModule()->getName()), 10);
            }
            //					Vytvoření objektu kontroleru
            $routes = new $routesClassName(null, $panelCat);
            $controllerClassName = ucfirst($panelCat->getModule()->getName()) . '_Panel';
            if (!class_exists($controllerClassName)) {
               throw new BadClassException(sprintf($this->tr('Nepodařilo se načíst třídu "%s" controlleru panelu modulu.'),
                     self::getCategory()->getModule()->getName()), 10);
            }

            //	Vytvoření objektu kontroleru
            $panelController = new $controllerClassName($panelCat, $routes);
            $panelController->run();
            array_push($this->coreTpl->panels[(string) $panel->{Model_Panel::COLUMN_POSITION}], $panelController->_getTemplateObj());
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
      $this->coreTpl->coreErrors = CoreErrors::getErrors();
      $this->coreTpl->coreErrorsEmpty = CoreErrors::isEmpty();
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
      $module = 'Module_'.ucfirst(self::$category->getModule()->getName());
      // forbiden access ?
      if( !self::$category->getRights()->isReadable() && !self::$category->getRights()->isWritable() && !self::$category->getRights()->isControll() ){
         $module = 'Module_DenyPage';
      }
      $className = $module;
      if(class_exists($className)){
         $ctrl = new $className(self::$category);
      } else {
         $ctrl = new Module_ErrPage(self::$category);
      }
      $ctrl->runController();
      // view metoda
      $viewM = 'run'.ucfirst(self::$urlRequest->getOutputType()).'View';
      if(method_exists($ctrl, $viewM) AND self::$urlRequest->getOutputType() != null AND self::$urlRequest->getOutputType() != 'html'){
         Template_Output::sendHeaders();
         $ctrl->{$viewM}();
      } else {
          $ctrl->runView();
          if(!AppCore::getUrlRequest()->isXHRRequest()){
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
         $pluginName = 'JsPlugin_' . ucfirst(self::$urlRequest->getName());
         if (class_exists($pluginName)) {
            $jsPlugin = new $pluginName();
         } else if (class_exists(ucfirst(self::$urlRequest->getName()))) {
            $pluginName = ucfirst(self::$urlRequest->getName());
            $jsPlugin = new $pluginName();
         } else {
            throw new UnexpectedValueException($this->tr('Neexistující JsPlugin'));
         }
         // vytvoření souboru
         Template_Output::sendHeaders();
         $jsPlugin->runAction(self::$urlRequest->getAction(), self::$urlRequest->getUrlParams(),
            self::$urlRequest->getOutputType());
      } catch (Exception $exc) {
         echo $exc->getMessage();
      }
      exit();
   }

   /**
    * Metoda pro spuštění akce na componentě
    */
   public function runComponent()
   {
      $componentName = 'Component_'.ucfirst(self::$urlRequest->getName());
         $component = new $componentName();
         // z komponenty patří výstup zde
         $component->runAction(self::$urlRequest->getAction(), self::$urlRequest->getUrlParams(),
            self::$urlRequest->getOutputType());
      }

   /**
    * Metoda načte soubor se specialními vlastnostmi přenesenými do šablony,
    * které jsou jednotné pro celý web
    * @todo předělat pro načítání z adresáře Webu, ne knihoven
    */
   private function initialWebSettings()
      {
      $fileName = 'initial'.ucfirst(self::$urlRequest->getOutputType()).'.php';
      if(file_exists(AppCore::getAppWebDir().self::ENGINE_CONFIG_DIR.DIRECTORY_SEPARATOR.$fileName)) {
         require AppCore::getAppWebDir().self::ENGINE_CONFIG_DIR.DIRECTORY_SEPARATOR.$fileName;
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
         // kontrola verze enginu
         $this->checkCoreVersion();
         // inicializace URL
         Url_Request::factory();
         //		inicializace sessions
         Session::factory();
         //		Inicializace chybových hlášek
         $this->_initMessagesAndErrors();

         /*
          * TODO: Tohle se musí doladit, protože to je začarovaný kruch.
          * Locales závisí na URL_Requestu a URL_Request zívisí na Auth a Auth na Locales
          */
         //inicializace lokalizace
         Locales::factory();
         // provedení autorizace
         Auth::authenticate();
         // výběr jazyka a locales
         self::$urlRequest = new Url_Request();
//         Locales::setLang(self::$urlRequest->getUrlLang());
         Locales::selectLang();
         // inicializace Šablonovacího systému
         Template::factory();
      } catch (Exception $exc) {
         if(strtolower($_SERVER['SERVER_NAME']) == 'localhost' OR strtolower($_SERVER['SERVER_NAME']) == '127.0.0.1'){
            echo $exc->getTraceAsString();
         } else {
            echo nl2br($exc->getMessage());
         }
         die ();
      }

      if(VVE_DEBUG_LEVEL >= 3 AND function_exists('xdebug_start_trace')){
         xdebug_start_trace(AppCore::getAppCacheDir().'trace.log');
      }

      // zapnutí buferu podle výstupu
      Template_Output::factory(self::$urlRequest->getOutputType());
      if(self::$urlRequest->getUrlLang() != null AND Locales::isLang(self::$urlRequest->getUrlLang())){
         $reqUrl = str_replace(self::$urlRequest->getUrlLang().URL_SEPARATOR, null, self::$urlRequest->getRequestUrl());
      } else {
         $reqUrl = self::$urlRequest->getRequestUrl();
      }
      $catUrl = self::$urlRequest->getCategory();

      $className = 'Module_'.ucfirst(self::$urlRequest->getCategory()).'_Category';
      // načtení kategorie
      if(self::$urlRequest->getUrlType() == Url_Request::URL_TYPE_CORE_MODULE AND class_exists($className)){ // Core Module
         self::$category = new $className(self::$urlRequest->getCategory(),true);
      } else if( ( ($reqUrl == '' AND $catUrl == null) OR ($reqUrl != '' AND $catUrl != null)) ) {
         if(!self::$urlRequest->isAdminCategory()){
            self::$category = new Category(self::$urlRequest->getCategory(),true);
         } else {
            self::$category = new Category_Admin(self::$urlRequest->getCategory(),true);
         }
         Url_Link::setCategory(self::$category->getUrlKey());
      } 
      if( ((self::$category instanceof Category_Core == false) OR !self::$category->isValid()) 
            AND self::$urlRequest->getUrlType() != Url_Request::URL_TYPE_MODULE_STATIC_REQUEST
         /*OR !self::$category->getRights()->isReadable()*/){ // Chyba stránky
         self::$category = new Module_ErrPage_Category(self::$urlRequest->getCategory(),true);
         Url_Link::setCategory(self::$category->getUrlKey());
         AppCore::setErrorPage(true);
      }
      unset ($className);
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
               trigger_error($this->tr("Neplatný požadavek na aplikaci"), E_USER_ERROR);
            }
            // render chyb
            if(!CoreErrors::isEmpty()) {
               CoreErrors::printErrors();
            }
         }
      }
      if(self::$urlRequest->isFullPage()
         OR (AppCore::isErrorPage() AND self::$urlRequest->getUrlType() == Url_Request::URL_TYPE_MODULE_RSS)){
         // Globální inicializace proměných do šablony
         $this->initialWebSettings();
         
         try {
            if(self::$urlRequest->getUrlType() == Url_Request::URL_TYPE_NORMAL AND !AppCore::isErrorPage()) {
               // zpracovávní modulu
               $this->runModule();
            } else if(self::$urlRequest->getUrlType() == Url_Request::URL_TYPE_MODULE_STATIC_REQUEST AND !AppCore::isErrorPage()) {
               // zpracovávní modulu ve static
               $this->runModuleStatic(true);
            }
            
            if(self::$urlRequest->getUrlType() == Url_Request::URL_TYPE_CORE_MODULE
                 OR AppCore::isErrorPage()) {
               // zpracování stránky enginu (sitemap, rss, error, atd.)
               $this->runCoreModule();
            }
         } catch (UnexpectedPageException $e) {
            AppCore::setErrorPage(true);
            $this->runCoreModule();
         } catch (Exception $e) {
            CoreErrors::addException($e);
         }

         //vytvoření hlavního menu
         $this->createMenus();
         try {
            // =========	spuštění panelů
            $this->runPanels();
         } catch (Exception $exc) {
            CoreErrors::addException($exc);
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
      if(VVE_DEBUG_LEVEL >= 3 AND function_exists('xdebug_stop_trace')){
         xdebug_stop_trace();
      }
      return true;
   }
}
?>
