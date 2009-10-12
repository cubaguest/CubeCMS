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
    * Adresář s helpery
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
    * prefix názvu sloupců s tabulkami u modulu (dbtable1, dbtable2, atd.)
    */
   //   const MODULE_DBTABLES_PREFIX = "dbtable";

   /**
    * Sufix pro metody kontroleru
    */
   //   const MODULE_CONTROLLER_SUFIX = 'Controller';

   /**
    * Sufix pro metody viewru
    */
   //   const MODULE_VIEWER_SUFIX = 'View';

   /**
    * Hlavní kontroler modulu - prefix
    */
   //   const MODULE_MAIN_CONTROLLER_PREFIX = 'Main';

   /**
    * Sufix názvu třídy panelů
    */
   //   const MODULE_PANEL_CLASS_SUFIX = '_Panel';

   /**
    * Název kontroleru panelu
    */
   //   const MODULE_PANEL_CONTROLLER = 'panelController';

   /**
    * Název viewru panelu
    */
   //   const MODULE_PANEL_VIEWER = 'panelView';

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
    * Nastavený debugovací level
    * @var integer
    */
//   private static $debugLevel = 1;

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
   private $urlRequest = null;

   /**
    * Objekt současné kategorie
    * @var Category
    */
   private static $category = null;

   /**
    * Pole s aktuálními informacemi o zpracovávané kategorii
    * @var array
    */
   //   private static $currentCategory = array();

   /**
    * objekt konfiguračního souboru
    * @var Config
    */
//   private static $sysConfig = null;

   /**
    * Proměná pro sledování doby generování scriptu
    * @var float
    */
   private $_startTime = null;

   /**
    * Proměná obsahuje jestli je zobrazeny chybová stránka
    * @var boolean
    */
   static private $isErrorPage = false;

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
      $direName = dirname(__FILE__); // OLD version + dává někdy špatný výsledek
      $realPath = realpath($direName); // OLD version + dává někdy špatný výsledek
      // $realPath = dirname(__FILE__); // ověřit v php 5.3.0 lze použít __DIR__
      $this->setAppMainDir($realPath.DIRECTORY_SEPARATOR);

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
      // inicializace funkcí pro překlady
      //      Locale::initTranslationsFunctions();
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
    * Metoda vrací objekt na systémovou konfiguraci
    * @return Config -- objekt konfigurace
    */
//   public static function sysConfig() {
//      return self::$sysConfig;
//   }

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
         if(!defined('VVE_'.$cfg[Model_Config::COLUMN_KEY])){
            define(strtoupper('VVE_'.$cfg[Model_Config::COLUMN_KEY]), $cfg[Model_Config::COLUMN_VALUE]);
         }
      }
//      self::$sysConfig = new Config(self::getAppWebDir() . DIRECTORY_SEPARATOR
//          . self::ENGINE_CONFIG_FILE);
   }

   /**
    * Metoda inicializuje šablonovací systém (SMARTY)
    */
   private function _initTemplate() {
      switch ($this->urlRequest->getOutputType()) {
         case 'js':
         // výstup jako js
            break;
         case 'html':
         case 'xhtml':
         case 'php':
         default:
            Template::factory();
            $this->coreTpl = new Template_Core();
            break;
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

         if(file_exists(AppCore::getAppWebDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR.$file)) {
            require_once AppCore::getAppWebDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR.$file;
         } else if(file_exists(AppCore::getAppWebDir().AppCore::ENGINE_LIB_DIR
                .DIRECTORY_SEPARATOR.$classL.DIRECTORY_SEPARATOR.$file)) {
               require_once AppCore::getAppWebDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR
                   .$classL.DIRECTORY_SEPARATOR.$file;
            } else if(file_exists(AppCore::getAppWebDir().AppCore::ENGINE_LIB_DIR
                   .DIRECTORY_SEPARATOR.$pathShort.DIRECTORY_SEPARATOR.$file)) {
                  require_once AppCore::getAppWebDir().AppCore::ENGINE_LIB_DIR
                      .DIRECTORY_SEPARATOR.$pathShort.DIRECTORY_SEPARATOR.$file;
               } else if(file_exists(AppCore::getAppWebDir().AppCore::ENGINE_LIB_DIR
                      .DIRECTORY_SEPARATOR.$pathFull.DIRECTORY_SEPARATOR.$file)) {
                     require_once AppCore::getAppWebDir().AppCore::ENGINE_LIB_DIR
                         .DIRECTORY_SEPARATOR.$pathFull.DIRECTORY_SEPARATOR.$file;
                  }


                  else if(file_exists(AppCore::getAppWebDir().AppCore::MODULES_DIR
                         .DIRECTORY_SEPARATOR.$pathShort.DIRECTORY_SEPARATOR.$moduleFile)) {
                        require_once AppCore::getAppWebDir().AppCore::MODULES_DIR
                            .DIRECTORY_SEPARATOR.$pathShort.DIRECTORY_SEPARATOR.$moduleFile;
                     } else {
                        var_dump($pathShort);
                        var_dump($pathDirs);
                        echo _("Chybějící třída<br />");
                        echo $classOrigName." ".$file." module-file: ".$moduleFile;
                     //                        throw new BadFileException(sprintf(_("Nebyla nalezena třída %s soubor %s"),
                     //                              $classOrigName, $file));
                     //            flush();
                     }
      }
      //		knihovny pro práci s chybami
      require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR
          . AppCore::ENGINE_EXCEPTIONS_DIR . DIRECTORY_SEPARATOR . 'coreException.class.php');
      require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR
          . AppCore::ENGINE_EXCEPTIONS_DIR . DIRECTORY_SEPARATOR . 'moduleException.class.php');
      require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR
          . AppCore::ENGINE_EXCEPTIONS_DIR . DIRECTORY_SEPARATOR . 'dbException.class.php');
      require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR
          . AppCore::ENGINE_EXCEPTIONS_DIR . DIRECTORY_SEPARATOR . 'badClassException.class.php');
      require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR
          . AppCore::ENGINE_EXCEPTIONS_DIR . DIRECTORY_SEPARATOR . 'badFileException.class.php');
      require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR
          . AppCore::ENGINE_EXCEPTIONS_DIR . DIRECTORY_SEPARATOR . 'imageException.class.php');
      // soubor s globálními funkcemi, které nejsou součástí php, ale časem by mohly být
      require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR
          . 'functions' . DIRECTORY_SEPARATOR . 'functions.php');
   }

   /**
    * Metoda inicializuje objekty pro práci s hláškami
    */
   private function _initMessagesAndErrors() {
   //		Vytvoření objektu pro práci se zprávami
   //      if(!Url_Request::get) {
      self::$messages = new Messages('session', 'messages', true);
      //      } else {
      //         self::$messages = new Messages('session', 'messages');
      //      }
      self::$userErrors = new Messages('session', 'errors');
   }

   /**
    * Medota pro inicializaci modulů
    */
   private function _initModules() {
   //      Module_Dirs::setWebDir('./'); //TODO patří přepsat tak aby se to zadávalo jinde
   //            Module_Dirs::setWebDataDir(AppCore::sysConfig()->getOptionValue("data_dir"));
   }

   /**
    * Metoda ověřuje autorizaci přístupu
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
   public function setAppMainDir($appMainDir) {
      self::$_appWebDir = $appMainDir;
   }

   /**
    * Metoda vytvoří hlavní menu aplikace
    */
   public function createMainMenu() {
         try {
            if(!file_exists('.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR
                  . 'menu.class.php')){
               throw new BadFileException(_('Soubor s třídou pro tvorbu menu nebyl nalezen'), 5);
            }
            require_once ('.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR
               . 'menu.class.php');
   
            if(!class_exists("Menu", false)){
               throw new BadClassException(_('Třídu pro tvorbu menu se nepodařilo načíst'),6);
            }
            $menu = new Menu();
            $menu->controller();
            $menu->view();
            $this->coreTpl->menuObj = $menu->template();
         } catch (Exception $e) {
            new CoreErrors($e);
         }
   }

   /**
    * Metoda přiřadí do šablony hlavní proměnné systému
    */
   public function assignMainVarsToTemplate() {
   //	Hlavni promene strany
      $this->coreTpl->pageTitle = VVE_WEB_NAME;
      if(!AppCore::isErrorPage()){
         $this->coreTpl->setCategoryName(Category::getMainCategory()->getLabel());
         $this->coreTpl->categoryId = Category::getMainCategory()->getId();
      }
      //adresa k rootu webu
      $this->coreTpl->rootDir = self::getAppWebDir();
      $link = new Links();
      $this->coreTpl->setPVar("mainWebDir", Url_Request::getBaseWebDir());
      $this->coreTpl->setPVar("imagesDir", Template::FACES_DIR.URL_SEPARATOR.Template::face()
          .URL_SEPARATOR.Template::IMAGES_DIR.URL_SEPARATOR);
      //$this->coreTpl->mainWebDir = Dispatcher::getBaseWebDir();
      //            $this->coreTpl->setVar("THIS_PAGE_LINK", (string)$link);
      // mapa webu
      //      $this->coreTpl->sitemapLink = (string)$link->clear(true)
      //          .Url_Request::getSpecialPageRegexp(Url_Request::SPECIAL_PAGE_SITEMAP);
      $this->coreTpl->mainLangImagesPath = VVE_IMAGES_LANGS_DIR.URL_SEPARATOR;
      unset($link);
      //Přihlášení uživatele
      $this->coreTpl->userIsLogin = AppCore::getAuth()->isLogin();
      $this->coreTpl->userLoginUsername = AppCore::getAuth()->getUserName();
      //Verze enginu
      $this->coreTpl->engineVersion = self::ENGINE_VERSION;
      // Přiřazení jazykového pole
      $this->coreTpl->setPVar("appLangsNames", Locale::getAppLangsNames());
      // Vytvoření odkazů s jazyky
      $langs = array();
      $langNames = Locale::getAppLangsNames();
      $link = new Links();
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
    * @todo Předělat, protože se nekontroluje existence jiných typů médií a
    * chybí propojení s kategorií, kde bude volba indexu také
    */
   public function renderTemplate() {
   //		načtení doby zpracovávání aplikace
      List ($usec, $sec) = Explode (' ', microtime());
      $endTime = ((float)$sec + (float)$usec);


      $this->coreTpl->execTime = round($endTime-$this->_startTime, 4);
      $this->coreTpl->countAllSqlQueries = Db_PDO::getCountQueries();
      //      //		Zvolení zobrazovaného média
      //      //		Medium pro tisk
      //      if(Dispatcher::getMediaType() == Dispatcher::MEDIA_TYPE_PRINT){
      ////         $this->template->display($faceFilePath."index-print.tpl");
      //         $this->template->display($faceFilePath."index-print.tpl");
      //      }
      //      //		Specielní media
      //      else if(file_exists($faceFilePath."index-".Dispatcher::getMediaType().".tpl")){
      ////         $this->template->display($faceFilePath."index-".Dispatcher::getMediaType().".tpl");
      //         $this->template->display($faceFilePath."index-".Dispatcher::getMediaType().".tpl");
      //      }
      //		Výchozí médium (www)
      //      else {
      //         $this->template->display($faceFilePath."index.tpl");
      $this->coreTpl->addTplFile("index.phtml", true);
      // render šablony
      $this->coreTpl->renderTemplate();
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
         $routes = new $routesClassName($this->urlRequest->getModuleUrlPart());
         // kontola cest
         $routes->checkRoutes();

         if(!$routes->getActionName()){
            AppCore::setErrorPage();
            return;
         }

//         $link = new Url_Link();
//         $link->category(self::getCategory()->getModuleName());
//         $link->setParams($this->urlRequest->getUrlParams());

      // načtení kontroleru
         $controllerClassName = ucfirst(self::getCategory()->getModule()->getName()).'_Controller';
         if(!class_exists($controllerClassName)) {
            throw new BadClassException(sprintf(_("Nepodařilo se načíst třídu '%s' controleru modulu."),
                self::getCategory()->getModule()->getName()), 10);
         }
         //					Vytvoření objektu kontroleru
         $controller = new $controllerClassName(self::getCategory(), $routes);
         $controller->runCtrl();
         
         // přiřazení šablony do výstupu
         $this->coreTpl->module = $controller->_getTemplateObj();

      } catch (Exception $e)  {
         new CoreErrors($e);
      }
 // TODO dodělat přesměrování při neexistencim, ale asi není nutné, protože by měla
 // být vyvolána chybová stránka typu 404
   //         if(new Links() == new Links(true)){
   //            throw new UnderflowException(_("Nepodařilo se nahrát prvky kategorie z databáze."), 16);
   //         } else {
   //            $redir = new Links(true);
   //            $redir->category()->action()->article()->rmParam()->reload();
   //         }
   //      }
   }

   /**
    * Metoda spustí samotný požadavek na modul, např generování listu v xml souboru
    */
   public function runModuleOnly() {
      ob_start(); // zapnutí buferu - kvůli výstupu

      // spuštění modulu
      try {
         if(self::getCategory() instanceof Category){
            throw new CoreException(_("Špatně zadaná požadavek na modul"));
         }

         // načtení a kontrola cest u modulu
         $routesClassName = ucfirst(self::getCategory()->getModule()->getName()).'_Routes';
         if(!class_exists($routesClassName)) {
            throw new BadClassException(sprintf(_("Nepodařilo se načíst třídu '%s' cest (routes) modulu."),
                self::getCategory()->getModule()->getName()), 10);
         }
         //	Vytvoření objektu s cestama modulu
         $routes = new $routesClassName($this->urlRequest->getModuleUrlPart());

      // načtení kontroleru
         $controllerClassName = ucfirst(self::getCategory()->getModule()->getName()).'_Controller';
         if(!class_exists($controllerClassName)) {
//            throw new BadClassException(sprintf(_("Nepodařilo se načíst třídu '%s' controleru modulu."),
//                self::getCategory()->getModule()->getName()), 10);
            trigger_error(sprintf(_("Nepodařilo se načíst třídu '%s' controleru modulu."),
                self::getCategory()->getModule()->getName()), 10);
         }
         //					Vytvoření objektu kontroleru
         $controller = new $controllerClassName(self::getCategory(), $routes);
         $controller->runCtrlAction($this->urlRequest->getAction(), $this->urlRequest->getOutputType());
         
      } catch (Exception $e){
         trigger_error($e->getMessage());
      }
      
      $content = ob_get_contents();
      // vytvoření objektu pro odeslání typu výstupu
      $output = new Template_Output($this->urlRequest->getOutputType());
      $output->addHeader("Content-Length: " . strlen($content));
      // odeslání hlaviček
      $output->sendHeaders();
      ob_flush();
   }

   /**
    * Metoda inicializuje a spustí panel
    * @param string $side -- jaký panel je spuštěn (left, right, bottom, top, ...)
    * @todo dodělat implementaci ostatních pozic panelů
    */
   public function runPanel($side) {
   //      //	Rozdělenní, který panel je zpracován
   //      $panelSideUpper = strtoupper($side);
   //      $panelSideLower = strtolower($side);
   //      //	Zapnutí panelu
   //
   //      $panelSides = array();
   //      $panelSides[$panelSideUpper] = true;
   //      if(!is_array($this->coreTpl->panel)){
   //         $this->coreTpl->panel = array();
   //      }
   //      $this->coreTpl->panel  = array_merge($this->coreTpl->panel, $panelSides);
   //
   //      // Načtení panelů
   //      $panelModel = new Model_Panel();
   //      $panelData = $panelModel->getPanel($side);
   //      if(!empty($panelData)){
   //         $panelsTempaltes = array();
   //         foreach ($panelData as $panel) {
   //            // Nastavení prováděné kategorie
   //            self::$currentCategory = $category
   //            = array(Model_Category::COLUMN_CAT_LABEL => $panel->{Model_Category::COLUMN_CAT_LABEL},
   //               Model_Category::COLUMN_CAT_ID => $panel->{Model_Category::COLUMN_CAT_ID});
   //            // Vytvoření tabulek
   ////            $tableIndex = 1; $moduleDbTables = array();
   ////            $objectName=self::MODULE_DBTABLES_PREFIX.$tableIndex;
   ////            while (isset($panel->$objectName) AND ($panel->$objectName != null)){
   ////               $moduleDbTables[$tableIndex] = $panel->$objectName;
   ////               $tableIndex++;
   ////               $objectName=self::MODULE_DBTABLES_PREFIX.$tableIndex;
   ////            }
   //            // systemový objekt
   //            $panelSys = new Module_Sys();
   //
   //            // Příprava modulu
   //            $panelSys->setModule(new Module($panel));
   //
   //            // nastavení locales
   //            $panelSys->setLocale(new Locale($panelSys->module()->getName()));
   //
   //            $panelClassName = ucfirst($panelSys->module()->getName()).self::MODULE_PANEL_CLASS_SUFIX;
   //            // Spuštění panelu
   //            try {
   //               //	vytvoření pole se skupinama a právama
   //               $userRights = array();
   //               foreach ($panel as $collum => $value){
   //                  if (substr($collum,0,strlen(Rights::RIGHTS_GROUPS_TABLE_PREFIX)) == Rights::RIGHTS_GROUPS_TABLE_PREFIX){
   //                     $userRights[substr($collum,strlen(Rights::RIGHTS_GROUPS_TABLE_PREFIX), strlen($collum))]=$value;
   //                  }
   //               }
   //               //	Vytvoření objektu pro přístup k právům modulu
   //               $panelSys->setRights(new Rights($userRights));
   //
   //               // objekt odkazu
   //               $link = new Links(true);
   //               $link->category($panel->{Model_Category::COLUMN_CAT_LABEL}, $panel->{Model_Category::COLUMN_CAT_ID});
   //               $panelSys->setLink($link);
   //
   //               if(class_exists($panelClassName)) {
   //                  //	CONTROLLER PANELU
   //                  $panelCtrl = new $panelClassName($panelSys);
   //
   //                  $panelCtrl->{self::MODULE_PANEL_CONTROLLER}();
   //
   //                  $panelCtrl->{self::MODULE_PANEL_VIEWER}();
   //                  array_push($panelsTempaltes, $panelCtrl->_getTemplateObj());
   //               } else {
   //                  throw new BadClassException(sprintf(_("Neexistující třída %s panelu"), $panelClassName), 23);
   //               }
   //            }  catch (ModuleException $e) {
   //               new CoreErrors($e);
   //            } catch (BadClassExceptio $e) {
   //               new CoreErrors($e);
   //            } catch (Exception $e) {
   //               new CoreErrors($e);
   //            }
   //         }
   //         $this->coreTpl->{$panelSideLower."Panel"} = $panelsTempaltes;
   //         unset ($panelsTempaltes);
   //      }
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
   }

   /**
    * Metoda vytváří sitemapu a odesílá ji na výstup
    */
   public function runSitemap() {
   //      $moduleSysMain = new Module_Sys();
   //      $sitemapItems = new Model_Sitemap($moduleSysMain);
   //      $sitemapItems = $sitemapItems->getItems();
   //      // vyttvoření hlavní kategorie
   //      $moduleSysMain->setLink(new Links(true, true));
   //      $sitemap = new SiteMap($moduleSysMain, AppCore::sysConfig()
   //          ->getOptionValue('sitemap_periode'), 1.0);
   //      $sitemap->run();
   //      unset ($sitemap);
   //      unset ($moduleSysMain);
   //      // procházení kategoríí na vytváření sitemapy
   //      if($sitemapItems != null) {
   //         foreach ($sitemapItems as $itemIndex => $item) {
   //            $moduleSys = new Module_Sys();
   //            //				Vytvoření objektu pro práci s modulem
   //            $moduleSys->setModule(new Module($item));
   //            $link = new Links(true);
   //            $moduleSys->setLink($link->category($item->{Model_Category::COLUMN_CAT_LABEL_ORIG},
   //                $item->{Model_Category::COLUMN_CAT_ID}));
   //            $moduleName = ucfirst($moduleSys->module()->getName());
   //            $moduleClass = $moduleName.'_'.self::MODULE_SITEMAP_SUFIX_CLASS;
   //            //				Pokud existuje soubor tak jej načteme
   //            if(file_exists($moduleSys->module()->getDir()->getMainDir(false).strtolower(self::MODULE_SITEMAP_SUFIX_CLASS).'.class.php')) {
   //            //               include_once ($module->getDir()->getMainDir(false).strtolower(self::MODULE_SITEMAP_SUFIX_CLASS).'.class.php');
   //               if(class_exists($moduleClass)) {
   //                  $sitemap = new $moduleClass($moduleSys, $item->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_FREQ},
   //                      (float)$item->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_PRIORITY});
   //                  // spuštění sitemapy
   //                  try {
   //                     $sitemap->run();
   //                  } catch (Exception $e) {
   //                     new CoreErrors($e);
   //                  }
   //
   //                  unset($sitemap);
   //               }
   //            } else {
   //            }
   //         }
   //         //	Vygenerování mapy	Podle vyhledávače a ukončení scriptu
   //         SiteMap::generateMap(Url_Request::getSupportedServicesName());
   //         exit ();
   //      }
   }

   /**
    * Metoda spouští kód pro zpracování chybné URL adresy
    */
   public function runErrorPage() {
   // Odeslání chybové hlavičky o nenalezení stránky
      header("HTTP/1.0 404 Not Found");
      //	Hlavni promene strany
      $this->coreTpl->pageNotFound = true;
   }

   /**
    * Metoda spouští kód pro generování specielních stránek
    */
   public function runSpecialPage() {
   //      $this->coreTpl->specialPage = true;
   //      $this->coreTpl->specialPageName = strtolower(Url_Request::getSpecialPage());
   //      switch (Url_Request::getSpecialPage()) {
   //         case Url_Request::SPECIAL_PAGE_SEARCH:
   //            $this->runSearchPage();
   //            break;
   //         case Url_Request::SPECIAL_PAGE_SITEMAP:
   //            $this->runSitemapPage();
   //            break;
   //         default:
   //            $this->setErrorPage();
   //            break;
   //      }
   }

   /**
    * Metoda pro spuštění hledání
    */
   public function runSearchPage() {
   //      $searchM = new Model_Search();
   //      $modules = $searchM->getModules();
   //      $itemsArray = $searchM->getItems();
   //      if($modules != null) {
   //         foreach ($modules as $itemIndex => $item) {
   //            $moduleSys = new Module_Sys();
   //            //				Vytvoření objektu pro práci s modulem
   //            $moduleSys->setModule(new Module($item));
   //            $moduleName = ucfirst($moduleSys->module()->getName());
   //            $moduleClass = $moduleName.'_'.self::MODULE_SEARCH_SUFIX_CLASS;
   //            //				Pokud existuje soubor tak jej načteme
   //            if(file_exists($moduleSys->module()->getDir()->getMainDir(false).strtolower(self::MODULE_SEARCH_SUFIX_CLASS).'.class.php')) {
   //               if(class_exists($moduleClass)) {
   //                  $searchObj = new $moduleClass($itemsArray[$moduleSys->module()->getIdModule()], $moduleSys);
   //                  // spuštění hledání v modulu
   //                  try {
   //                     $searchObj->runSearch();
   //                  } catch (Exception $e) {
   //                     new CoreErrors($e);
   //                  }
   //                  unset($searchObj);
   //               }
   //            }
   //         // vyprázdnění modulu
   //         }
   //         $this->coreTpl->setCategoryName(_("hledání"));
   //         $this->coreTpl->setArticleName(htmlspecialchars(rawurldecode($this->coreTpl->get('search',null,false))));
   //         $this->coreTpl->searchResults = Search::getResults();
   //         $this->coreTpl->searchResultsCount = Search::getNumResults();
   //      }
   }

   /**
    * Metoda spouští generování stránky s mapou webu
    */
   public function runSitemapPage() {
   //      $sitemapItems = new Model_Sitemap(new Module_Sys());
   //      $sitemapItems = $sitemapItems->getItemsOrderBySections();
   //      //      // procházení kategoríí na vytváření sitemapy
   //      $sitemapArray = array();
   //      if(!empty ($sitemapItems)) {
   //         foreach ($sitemapItems as $item) {
   //            $idSection = $item->{Model_Sections::COLUMN_SEC_ID};
   //            if(!isset ($sitemapArray[$idSection])) {
   //               $sitemapArray[$idSection] =
   //                   array('name' =>$item->{Model_Sections::COLUMN_SEC_LABEL},
   //                   'categories' => array());
   //            }
   //            $idCategory = $item->{Model_Category::COLUMN_CAT_ID};
   //            // systémový objekt modulu
   //            $moduleSys = new Module_Sys();
   //            //				Vytvoření objektu pro práci s modulem
   //            $moduleSys->setModule(new Module($item));
   //            $link = new Links(true);
   //            $link->category($item->{Model_Category::COLUMN_CAT_LABEL}, $idCategory);
   //            $moduleSys->setLink($link);
   //            //            vytvoření pole s kategorií
   //            if(!isset ($sitemapArray[$idSection]['categories'][$idCategory])) {
   //               $sitemapArray[$idSection]['categories'][$idCategory] = array(
   //                   'name' => $item->{Model_Category::COLUMN_CAT_LABEL},
   //                   'url' => (string)$moduleSys->link(),
   //                   'results' => array());
   //            }
   //            $moduleName = ucfirst($moduleSys->module()->getName());
   //            $moduleClass = $moduleName.'_'.self::MODULE_SITEMAP_SUFIX_CLASS;
   //            //				Pokud existuje soubor tak jej načteme
   //            if(file_exists($moduleSys->module()->getDir()->getMainDir(false).strtolower(self::MODULE_SITEMAP_SUFIX_CLASS).'.class.php')) {
   //            //               include_once ($moduleSys->module()->getDir()->getMainDir(false).strtolower(self::MODULE_SITEMAP_SUFIX_CLASS).'.class.php');
   //               if(class_exists($moduleClass)) {
   //                  $sitemap = new $moduleClass($moduleSys, $item->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_FREQ},
   //                      $item->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_PRIORITY});
   //                  // spuštění sitemapy
   //                  try {
   //                     $sitemap->run();
   //                  } catch (Exception $e) {
   //                     new CoreErrors($e);
   //                  }
   //
   //                  $sitemapArray[$idSection]['categories'][$idCategory]['results'] =
   //                      array_merge($sitemapArray[$idSection]['categories'][$idCategory]['results'],
   //                      $sitemap->getCurrentMapArray());
   //                  unset($sitemap);
   //               }
   //            }
   //         }
   //      }
   //      $this->coreTpl->sitemapPages = $sitemapArray;
   }

   /**
    * Metoda spustí akci nad JsPluginem
    */
   public function runJsPlugin() {
      $pluginName = 'JsPlugin_'.ucfirst($this->urlRequest->getName());

      $jsPlugin = new $pluginName();
      
      // vytvoření souboru
//      $file = new JsPlugin_File($this->urlRequest->getAction().'.'.$this->urlRequest->getOutputType(), true);
//      $file->setParams($this->urlRequest->getUrlParams());
      ob_start();
      $jsPlugin->runAction($this->urlRequest->getAction(), $this->urlRequest->getUrlParams(),
      $this->urlRequest->getOutputType());
//      $jsPlugin->{$this->urlRequest->getAction()}($file);
      $content = ob_get_contents();
      // vytvoření objektu pro odeslání typu výstupu
      $output = new Template_Output($this->urlRequest->getOutputType());
      $output->addHeader("Content-Length: " . strlen($content));
      // odeslání hlaviček
      $output->sendHeaders();
      ob_flush();
   }

   /**
    * Metoda pro spuštění akce na componentě
    */
   public function runComponent() {
      $componentName = 'Component_'.ucfirst($this->urlRequest->getName());

      $component = new $componentName();

      ob_start();
      $component->runAction($this->urlRequest->getAction(), $this->urlRequest->getUrlParams(),
      $this->urlRequest->getOutputType());

      $content = ob_get_contents();
      // vytvoření objektu pro odeslání typu výstupu
      $output = new Template_Output($this->urlRequest->getOutputType());
      $output->addHeader("Content-Length: " . strlen($content));
      // odeslání hlaviček
      $output->sendHeaders();
      ob_flush();
   }

   /**
    * Metoda načte soubor se specialními vlastnostmi přenesenými do šablony,
    * které jsou jednotné pro celý web
    */
   private function initialWebSettings() {
      $fileName = 'initial'.ucfirst($this->urlRequest->getOutputType()).'.php';
      if(file_exists('./'.self::MODULES_DIR.DIRECTORY_SEPARATOR.$fileName)) {
         require './'.self::MODULES_DIR.DIRECTORY_SEPARATOR.$fileName;
      }
   }

   /**
    * Hlavní metoda provádění aplikace
    */
   public function runCore() {
      $this->urlRequest = new Url_Request();
      Locale::setLang($this->urlRequest->getUrlLang());

      // načtení kategorie
      self::$category = new Category($this->urlRequest->getCategory(),true);

      Url_Link::setCategory(self::$category->getUrlKey());

      // kontrola jestli zadaná kategorie vůbec existuje
      if(!self::$category->isValid()) {
         AppCore::setErrorPage();
      }

      // podle typu vstupu inicializujeme výstup // @todo není implementováno
      $this->_initTemplate();

      // vybrání části pro zpracování podle požadavku
      switch ($this->urlRequest->getUrlType()) {
         case Url_Request::URL_TYPE_MODULE:
            $this->runModuleOnly();
            break;
         case Url_Request::URL_TYPE_ENGINE_PAGE:
            print "special page";
            break;
         case Url_Request::URL_TYPE_COMPONENT:
            $this->runComponent();
            break;
         case Url_Request::URL_TYPE_JSPLUGIN:
            $this->runJsPlugin();
            break;
         case Url_Request::URL_TYPE_NORMAL:
         default:
            // Globální inicializace proměných do šablony
            $this->initialWebSettings();
            //vytvoření hlavního menu
            $this->createMainMenu();

            // Pokud není chyba spustíme moduly
            if(!AppCore::isErrorPage()) {
            //		spuštění modulů
               $this->runModule();
               // =========	spuštění panelů
               //		Levý
               if(self::$category->isLeftPanel()) {
                  $this->runPanel('left');
               }
               //		Pravý
               if(self::$category->isRightPanel()) {
                  $this->runPanel('right');
               }
            }

            
            // pokud se v aplikaci vyskitla chybová stránka, spustíme ji
            if(AppCore::isErrorPage()) {
               $this->runErrorPage();
               $this->runPanel('left');
               $this->runPanel('right');
            }
            //		Přiřazení proměných modulů do šablony
            $this->assignMessagesToTpl();
            //		přiřazení hlavních proměných
            $this->assignMainVarsToTemplate();
            //		přiřazení chbových hlášek do šablony
            $this->assignCoreErrorsToTpl();
            //		render šablony
            $this->renderTemplate();
            return true;
            break;
      }

      //      if($this->urlRequest->isNormalUrl()) {
      //      // je zpracovávána normální URL
      //         print "normal URL";
      //
      //      } else {
      //      // je zpracovávána URL pro podpůrné služby
      //      }


      //	Inicializace modulu
      //            $this->_initModules();
      //	inicializace šablonovacího systému
      //      $this->_initTemplate();
      // pokud je spuštěna služba Supported Services (Eplugin, JsPlugin, Sitemap, RSS, Atom)
      //      if(Url_Request::isSupportedServices()){
      //         //		Pokud se načítá statická část epluginu
      //         if(Url_Request::getSupportedServicesType() == Url_Request::SUPPORTSERVICES_EPLUGIN_NAME){
      //            //					$epluginName = ucfirst(Eplugin::getSelEpluginName());
      //            $epluginName = ucfirst(Url_Request::getSupportedServicesName());
      //            $epluginWithOutEplugin = $epluginName.'Eplugin';
      //            /**
      //             * @todo co to sakra je? proč je to tu dvakrát? a kde je předání parametrů?
      //             */
      //            if(class_exists($epluginName)){
      //               $eplugin = new $epluginName();
      //               $eplugin->initRunOnlyEplugin();
      //               return true;
      //            } else if(class_exists($epluginWithOutEplugin)){
      //               $eplugin = new $epluginWithOutEplugin();
      //               $eplugin->initRunOnlyEplugin();
      //               return true;
      //            } else {
      //               new CoreException(_('Požadovaný eplugin nebyl nalezen'), 21);
      //            }
      //            unset($epluginName);
      //         }
      //         //		Pokud se načítá statická část jspluginu
      //         else if(Url_Request::getSupportedServicesType() == Url_Request::SUPPORTSERVICES_JSPLUGIN_NAME){
      //            //					$jspluginName = ucfirst(JsPlugin::getSelJspluginName());
      //            $jspluginName = ucfirst(Url_Request::getSupportedServicesName());
      //            if(class_exists($jspluginName)){
      //               $jsplugin = new $jspluginName();
      //               return true;
      //            } else {
      //               new CoreException(_('Požadovaný jsplugin nebyl nalezen'), 22);
      //            }
      //            unset($jspluginName);
      //         }
      //         //		Pokud se načítá statická část sitemap
      //         else if(Url_Request::getSupportedServicesType() == Url_Request::SUPPORTSERVICES_SITEMAP_NAME){
      //            $this->runSitemap();
      //         }
      //      }
      //      // pokud je spuštěn ajax požadavek
      //      else if(Url_Request::isAjaxRequest()){
      //         try {
      //            if(Ajax::getAjaxType() == Ajax_Link::AJAX_EPLUGIN_NAME){
      //               $epluginName = Eplugin::PARAMS_EPLUGIN_FILE_PREFIX.'_'.Ajax::getAjaxName();
      //               if(!class_exists($epluginName)) {
      //                  throw new BadClassException(_('Neplatný typ Epluginu'), 23);
      //               }
      //               $eplugin = new $epluginName();
      //               $ajaxObj = new Ajax();
      //               if(!method_exists($eplugin, $ajaxObj->getMetod())){
      //                  throw new BadClassException(sprintf(_('Neexistující ajax metoda "%s" Epluginu'),$ajaxObj->getMetod()), 24);
      //               }
      //               $eplugin->{$ajaxObj->getMetod()}($ajaxObj);
      //            } else if(Ajax::getAjaxType() == Ajax_Link::AJAX_MODULE_NAME){
      //               $this->runModuleAjax();
      //               return true;
      //            }
      //         } catch (Exception $e) {
      //            new CoreErrors($e);
      //         }
      //
      //         if(!AppCore::getInfoMessages()->isEmpty()){
      //            echo AppCore::getInfoMessages();
      //         }
      //
      //         if(!AppCore::getUserErrors()->isEmpty()){
      //            echo AppCore::getUserErrors();
      //         }
      //
      //         if(!CoreErrors::isEmpty()){
      //            echo CoreErrors::getLastError();
      //         }
      //         exit();
      //      }
      //      // pokud je zpracovávána normální aplikace a její moduly
      //      else {
      //         // změna zachytávače chyb
      //         $old_error_handler = set_error_handler("CoreErrors::errorHandler");
      //         // výběr média
      //         switch (Url_Request::getMediaType()) {
      //            // Volba typu média
      //            case Url_Request::MEDIA_TYPE_WWW:
      //               default:
      //                  //				Výchozí je zobrazena stránka =======================================
      //                  //nastavení vybrané kategorie
      //                  $this->selectCategory();
      //                  // Globální inicializace proměných do šablony
      //                  $this->initialWebSettings();
      //                  //vytvoření hlavního menu
      //                  $this->createMainMenu();
      //
      //                  if(Url_Request::isSpecialPage()){
      //                     $this->runSpecialPage();
      //                  }
      //                  // Pokud není chyba spustíme moduly
      //                  if(!AppCore::isErrorPage() AND !Url_Request::isSpecialPage()){
      //                     //		spuštění modulů
      //                     $this->runModules();
      //                  }
      //
      //                  // =========	spuštění panelů
      //                  //		Levý
      //                  if(Category::isLeftPanel()){
      //                     $this->runPanel('left');
      //                  }
      //                  //		Pravý
      //                  if(Category::isRightPanel()){
      //                     $this->runPanel('right');
      //                  }
      //                  // pokud se v aplikaci vyskitla chybová stránka, spustíme ji
      //                  if(AppCore::isErrorPage()){
      //                     $this->runErrorPage();
      //                  }
      //                  //		Přiřazení proměných modulů do šablony
      //                  $this->assignMessagesToTpl();
      //                  //		přiřazení hlavních proměných
      //                  $this->assignMainVarsToTemplate();
      //                  //		přiřazení chbových hlášek do šablony
      //                  $this->assignCoreErrorsToTpl();
      //                  //		render šablony
      //                  $this->renderTemplate();
      //                  return true;
      //               }
      //            }
//      print ("Aplikace OK");
      return true;
   }
}
      ?>