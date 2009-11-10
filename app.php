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
   private $urlRequest = null;

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
//      $this->setAppMainDir($realPath.DIRECTORY_SEPARATOR);

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
            if($cfg[Model_Config::COLUMN_KEY] == 'true'){
               define(strtoupper('VVE_'.$cfg[Model_Config::COLUMN_KEY]), true);
            } else if($cfg[Model_Config::COLUMN_KEY] == 'false'){
               define(strtoupper('VVE_'.$cfg[Model_Config::COLUMN_KEY]), false);
            } else {
               define(strtoupper('VVE_'.$cfg[Model_Config::COLUMN_KEY]), $cfg[Model_Config::COLUMN_VALUE]);
            }
            
         }
      }
   }

   /**
    * Metoda inicializuje šablonovací systém (SMARTY)
    */
   private function _initTemplate() {
//      switch ($this->urlRequest->getOutputType()) {
//         case 'js':
//         // výstup jako js
//            break;
//         case 'html':
//         case 'xhtml':
//         case 'php':
//         default:
//            break;
//      }
      
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
                  }


                  else if(file_exists(AppCore::getAppLibDir().AppCore::MODULES_DIR
                         .DIRECTORY_SEPARATOR.$pathShort.DIRECTORY_SEPARATOR.$moduleFile)) {
                        require_once AppCore::getAppLibDir().AppCore::MODULES_DIR
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
      //adresa k rootu webu
      $this->coreTpl->rootDir = self::getAppWebDir();
      $link = new Links();
      $this->coreTpl->setPVar("mainWebDir", Url_Request::getBaseWebDir());
      $this->coreTpl->setPVar("imagesDir", Template::FACES_DIR.URL_SEPARATOR.Template::face()
          .URL_SEPARATOR.Template::IMAGES_DIR.URL_SEPARATOR);
      $this->coreTpl->mainLangImagesPath = VVE_IMAGES_LANGS_DIR.URL_SEPARATOR;
      unset($link);
      //Přihlášení uživatele
      $this->coreTpl->userIsLogin = AppCore::getAuth()->isLogin();
      $this->coreTpl->userLoginUsername = AppCore::getAuth()->getUserName();
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
    * @todo Předělat, protože se nekontroluje existence jiných typů médií
    */
   public function renderTemplate() {
   //		načtení doby zpracovávání aplikace
      List ($usec, $sec) = Explode (' ', microtime());
      $endTime = ((float)$sec + (float)$usec);
      $this->coreTpl->execTime = round($endTime-$this->_startTime, 4);
      $this->coreTpl->countAllSqlQueries = Db_PDO::getCountQueries();
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
            return false;
         }

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
   }

   /**
    * Metoda spustí samotný požadavek na modul, např generování listu v xml souboru
    */
   public function runModuleOnly() {
      // spuštění modulu
      try {
         if(!self::getCategory() instanceof Category){
            throw new CoreException(_("Špatně zadaný požadavek na modul"));
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
            trigger_error(sprintf(_("Nepodařilo se načíst třídu '%s' controleru modulu."),
                self::getCategory()->getModule()->getName()), 10);
         }
         //					Vytvoření objektu kontroleru
         $controller = new $controllerClassName(self::getCategory(), $routes);
         $controller->runCtrlAction($this->urlRequest->getAction(), $this->urlRequest->getOutputType());
      } catch (Exception $e){
         trigger_error($e->getMessage());
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
      if(self::$category->isIndividualPanels()){
         $panels = $panelsM->getPanelsList(self::$category->getId());
      } else {
         $panels = $panelsM->getPanelsList();
      }

      foreach ($panels as $panel) {
         $panelCat = new Category(null, false, $panel);
         if(!file_exists(AppCore::getAppLibDir().self::MODULES_DIR.DIRECTORY_SEPARATOR
               .$panelCat->getModule()->getName().DIRECTORY_SEPARATOR.'panel.class.php')){
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
               .$catObj->getModule()->getName().DIRECTORY_SEPARATOR.'sitemap.class.php')){

            $sitemap = new SiteMap($catObj, $routes, $category->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_FREQ},
               $category->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_PRIORITY});
         } else {
            $sClassName = ucfirst($catObj->getModule()->getName()).'_Sitemap';
            $sitemap = new $sClassName($catObj, $routes, $category->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_FREQ},
               $category->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_PRIORITY});
         }
         $sitemap->run();
         unset ($sitemap);
      }

      SiteMap::generateMap($this->urlRequest->getOutputType());

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
    * Metoda spouští kód pro generování specielních stránek
    */
   public function runSpecialPage() {
      $this->coreTpl->specialPage = true;
      switch ($this->urlRequest->getName()) {
         case 'sitemap':
            $this->runSitemapPage();
            break;
         case 'search':
            $this->runSearchPage();
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
         switch ($this->urlRequest->getName()) {
            case 'sitemap':
               $this->runSitemap();
               break;
         }
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
               .$catObj->getModule()->getName().DIRECTORY_SEPARATOR.'sitemap.class.php')){

            $sitemap = new SiteMap($catObj, $routes, $category->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_FREQ},
               $category->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_PRIORITY});
         } else {
            $sClassName = ucfirst($catObj->getModule()->getName()).'_Sitemap';
            $sitemap = new $sClassName($catObj, $routes, $category->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_FREQ},
               $category->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_PRIORITY});
         }
         $sitemap->run();
         $catArr[$catObj->getId()] = $sitemap->createMapArray();
      }

//      var_dump($catArr);
      $sitemapTpl->catArr = $catArr;

      $this->coreTpl->specialPageTpl = $sitemapTpl;

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
      $jsPlugin->runAction($this->urlRequest->getAction(), $this->urlRequest->getUrlParams(),
      $this->urlRequest->getOutputType());
   }

   /**
    * Metoda pro spuštění akce na componentě
    */
   public function runComponent() {
      $componentName = 'Component_'.ucfirst($this->urlRequest->getName());
      $component = new $componentName();

      $component->runAction($this->urlRequest->getAction(), $this->urlRequest->getUrlParams(),
      $this->urlRequest->getOutputType());
   }

   /**
    * Metoda načte soubor se specialními vlastnostmi přenesenými do šablony,
    * které jsou jednotné pro celý web
    * @todo předělat pro načítání z adresáře Webu, ne knihoven
    */
   private function initialWebSettings() {
      $fileName = 'initial'.ucfirst($this->urlRequest->getOutputType()).'.php';
      if(file_exists(AppCore::getAppWebDir().self::ENGINE_CONFIG_DIR.DIRECTORY_SEPARATOR.$fileName)) {
         require AppCore::getAppWebDir().self::ENGINE_CONFIG_DIR.DIRECTORY_SEPARATOR.$fileName;
      }
   }

   /**
    * Hlavní metoda provádění aplikace
    */
   public function runCore() {
      ob_start();
      $this->urlRequest = new Url_Request();
      Locale::setLang($this->urlRequest->getUrlLang());

      // načtení kategorie
      self::$category = new Category($this->urlRequest->getCategory(),true);
      Url_Link::setCategory(self::$category->getUrlKey());
      if(!self::getCategory()->isValid()) {
         AppCore::setErrorPage();
      }

      // zapnutí buferu
      $output = new Template_Output($this->urlRequest->getOutputType());

      if(!$this->urlRequest->isFullPage()) {
         // vynulování chyby, protože chybová stránka je výchozí stránka
         AppCore::setErrorPage(false);
      // je zpracovává pouze požadavek na část aplikace
      // vybrání části pro zpracování podle požadavku
         switch ($this->urlRequest->getUrlType()) {
            case Url_Request::URL_TYPE_MODULE_REQUEST:
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
      } else {
      // je zpracovávána stránka aplikace
         $this->coreTpl = new Template_Core();

         // Globální inicializace proměných do šablony
         $this->initialWebSettings();
            //vytvoření hlavního menu
         $this->createMainMenu();

         if($this->urlRequest->getUrlType() == Url_Request::URL_TYPE_ENGINE_PAGE
            AND !AppCore::isErrorPage()) {
         // zpracování stránky enginu (sitemap, search, atd.)
            $this->runSpecialPage();
         } else if(!AppCore::isErrorPage()) {
            // přiřazení kategorrie do nadpisu
            $this->coreTpl->addPageHeadline(Category::getSelectedCategory()->getLabel());
            $this->coreTpl->categoryId = Category::getSelectedCategory()->getId();
            $this->coreTpl->setPVar('CURRENT_CATEGORY_PATH',
               Menu_Main::getMenuObj()->getPath(Category::getSelectedCategory()->getId()));
            // zpracovávní modulu
            $this->runModule();
         }

         // =========	spuštění panelů
         $this->runPanels();

         // pokud je chyba vykreslíme chybovou stránku
         if(AppCore::isErrorPage()) {
            $this->coreTpl->setPVar('CURRENT_CATEGORY_PATH', array(_('stránka nenalezena')));
            $this->coreTpl->pageNotFound = true;
         }

         // vložení proměných šablony z jadra
         $this->assignMainVarsToTemplate();
         //	Přiřazení hlášek do šablony
         $this->assignMessagesToTpl();

         // přiřazení chybových hlášek jádra do šablony
         $this->assignCoreErrorsToTpl();
         //	render šablony
         $this->renderTemplate();
      }

      if(AppCore::isErrorPage()) {
         $output->addHeader("HTTP/1.0 404 Not Found");
      }
      $content = ob_get_contents();
      // odeslání potřebných hlaviček a délky řetězců
      $output->setContentLenght(strlen($content));
      // odeslání hlaviček
      $output->sendHeaders();
      ob_flush();

      return true;
//      // vybrání části pro zpracování podle požadavku
//      switch ($this->urlRequest->getUrlType()) {
//         case Url_Request::URL_TYPE_MODULE_REQUEST:
//            $this->runModuleOnly();
//            break;
//         case Url_Request::URL_TYPE_SUPPORT_SERVICE:
//            $this->runSupportServices();
//            break;
//         case Url_Request::URL_TYPE_COMPONENT_REQUEST:
//            $this->runComponent();
//            break;
//         case Url_Request::URL_TYPE_JSPLUGIN_REQUEST:
//            $this->runJsPlugin();
//            break;
//         case Url_Request::URL_TYPE_NORMAL:
//         default:
//
//         case Url_Request::URL_TYPE_ENGINE_PAGE:
//            if(!self::$category->isValid()) {
//               AppCore::setErrorPage();
//            }
//
//
//            // Globální inicializace proměných do šablony
//            $this->initialWebSettings();
//            //vytvoření hlavního menu
//            $this->createMainMenu();
//
//            //	přiřazení hlavních proměných
//            $this->assignMainVarsToTemplate();
//
//            // Pokud není chyba spustíme moduly
//            if(!AppCore::isErrorPage()) {
//               if($this->urlRequest->getUrlType() == Url_Request::URL_TYPE_ENGINE_PAGE) {
//                  // spuštění specielní stránky (hledání, mapa, atd.)
//                  $this->runSpecialPage();
//               } else {
//                  //	spuštění modulů
//                  $this->runModule();
//               }
//            }
//
//            // pokud se v aplikaci vyskitla chybová stránka, spustíme ji
//            if(AppCore::isErrorPage()) {
//               $this->runErrorPage();
//            }
//            // =========	spuštění panelů
//            $this->runPanels();
//
//            //		Přiřazení proměných modulů do šablony
//            $this->assignMessagesToTpl();
//
//            //		přiřazení chbových hlášek do šablony
//            $this->assignCoreErrorsToTpl();
//            //		render šablony
//            $this->renderTemplate();
//            return true;
//            break;
//      }
   }
}
      ?>