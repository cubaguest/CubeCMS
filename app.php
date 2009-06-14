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
    * Výchozí cestak enginu
    */
   const MAIN_ENGINE_PATH = './';

   /**
    * Obsahuje hlavní soubor aplikace
    */
   const APP_MAIN_FILE = 'index.php';

   /**
    * Název konfiguračního souboru
    */
   const MAIN_CONFIG_FILE = "config.xml";

   /**
    * Název sekce v konf. souboru s konfigurací databáze
    */
   const MAIN_CONFIG_DB_SECTION = "db";

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
    * Adresář s pluginy filesystému
    */
   const ENGINE_FILESYSTEM_DIR = 'filesystem';

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
   const MODULE_DBTABLES_PREFIX = "dbtable";

   /**
    * Sufix pro metody kontroleru
    */
   const MODULE_CONTROLLER_SUFIX = 'Controller';

   /**
    * Sufix pro metody viewru
    */
   const MODULE_VIEWER_SUFIX = 'View';

   /**
    * Hlavní kontroler modulu - prefix
    */
   const MODULE_MAIN_CONTROLLER_PREFIX = 'Main';

   /**
    * Sufix názvu třídy panelů
    */
   const MODULE_PANEL_CLASS_SUFIX = '_Panel';

   /**
    * Název kontroleru panelu
    */
   const MODULE_PANEL_CONTROLLER = 'panelController';

   /**
    * Název viewru panelu
    */
   const MODULE_PANEL_VIEWER = 'panelView';

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
   private static $debugLevel = 1;

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
    * objekt db konektory
    * @var DbConnector
    */
   private static $dbConnector = null;

   /**
    * Pole s aktuálními informacemi o zpracovávané kategorii
    * @var array
    */
   private static $currentCategory = array();

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
   private function __construct(){
      //		Definice globálních konstant
      define('URL_SEPARATOR', '/');

      //		inicializace stratovacího času
      List ($usec, $sec) = Explode (' ', microtime());
      $this->_stratTime=((float)$sec + (float)$usec);

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
      // načtení systémového konfiguračního souboru
      $this->_initConfig();

      //		Zapnutí debugeru
      self::$debugLevel = self::sysConfig()->getOptionValue('DEBUG_LEVEL');

      //		Definování konstanty s datovým adresářem modulů //TODO upravit tak aby se to používalo korektně např. proměná
      define('MAIN_DATA_DIR', self::sysConfig()->getOptionValue('data_dir'));

      //inicializace lokalizace
      Locale::factory();
      //		inicializace URL
      UrlRequest::factory();
      //		inicializace sessions
      Sessions::factory(self::sysConfig()->getOptionValue('session_name'));
      // výběr jazyka a locales
      Locale::selectLang();
      //		inicializace db konektoru
      $this->_initDbConnector();
      //		Inicializace chybových hlášek
      $this->_initMessagesAndErrors();
      // inicializace funkcí pro překlady
      //      Locale::initTranslationsFunctions();
      //Spuštění jádra aplikace
      $this->runApp();
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
   public static function getInstance(){
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
   public static function getAppWebDir(){
      return self::$_appWebDir;
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
   public static function sysConfig() {
      return self::$sysConfig;
   }

   /**
    * Metoda vrací adresář ke zvolenému vzhledu
    *
    * @param boolean $fullDir -- jestli se má vrátit celá cesta nebo jemo část od hlavního adresáře
    * @param boolean $withFacesDir -- jestli se má vrátit celá adresář faces (true)
    * @return string -- adresář zvoleného vzhledu
    */
   //   public static function getTepmlateFaceDir($fullDir = true, $withFacesDir = true) {
   //      if($fullDir){
   //         return self::$_appWebDir.DIRECTORY_SEPARATOR.self::FACES_DIR.DIRECTORY_SEPARATOR
   //            .self::$templateFaceDir.DIRECTORY_SEPARATOR;
   //      } else {
   //         if($withFacesDir){
   //            return self::FACES_DIR.URL_SEPARATOR.self::$templateFaceDir.URL_SEPARATOR;
   //         } else {
   //            return self::$templateFaceDir.URL_SEPARATOR;
   //         }
   //      }
   //   }

   /**
    * Metoda vrací adresář k výchozímu vzhledu
    *
    * @param boolean $fullDir -- jestli se má vrátit celá cesta nebo jemo část od hlavního adresáře
    * @return string -- adresář výchozího vzhledu
    */
   //   public static function getTepmlateDefaultFaceDir($fullDir = true) {
   //      if($fullDir){
   //         return self::$_appWebDir.DIRECTORY_SEPARATOR.self::FACES_DIR.DIRECTORY_SEPARATOR
   //            .self::$templateDefaultFaceDir.DIRECTORY_SEPARATOR;
   //      } else {
   //         return self::FACES_DIR.URL_SEPARATOR.self::$templateDefaultFaceDir.URL_SEPARATOR;
   //      }
   //   }

   /**
    * Metoda vrací objekt db conektoru
    *
    * @return DbInterface -- objekt db konektoru
    */
   public static function getDbConnector() {
      return self::$dbConnector;
   }

   /**
    * Metoda vrací objekt aktuálního modulu
    *
    * @return Module -- objekt vybraného modulu
    */
   //   public static function getSelectedModule() {
   //      return self::$selectedModule;
   //   }

   /**
    * Metoda nastavuje objekt aktuálního modulu
    * @param Module $module -- (option) objekt vybraného modulu
    */
   //   public static function setSelectedModule($module = null) {
   //      self::$selectedModule = $module;
   //   }

   /**
    * Metoda vrací onformace o práve zpracovávané kategori nebo false
    * @return array -- právě zpracovávaná kategorie (název, id)
    */
   public static function getSellectedCategory() {
      if(!empty (self::$currentCategory)){
         return self::$currentCategory;
      } else {
         return false;
      }
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
   public static function getAuth(){
      return self::$auth;
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
   private function _initDbConnector() {
      require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'db'
         . DIRECTORY_SEPARATOR. 'db.class.php');
      try {
         self::$dbConnector = Db::factory(self::sysConfig()->getOptionValue("dbhandler",
               self::MAIN_CONFIG_DB_SECTION),
            self::sysConfig()->getOptionValue("dbserver", self::MAIN_CONFIG_DB_SECTION),
            self::sysConfig()->getOptionValue("dbuser", self::MAIN_CONFIG_DB_SECTION),
            self::sysConfig()->getOptionValue("dbpasswd", self::MAIN_CONFIG_DB_SECTION),
            self::sysConfig()->getOptionValue("dbname", self::MAIN_CONFIG_DB_SECTION),
            self::sysConfig()->getOptionValue("tbprefix", self::MAIN_CONFIG_DB_SECTION));

         if(self::$dbConnector == false){
            throw new UnexpectedValueException(self::sysConfig()->getOptionValue("dbhandler",
                  self::MAIN_CONFIG_DB_SECTION)._(" Databázový engine nebyl implementován"), 3);
         }
      } catch (UnexpectedValueException $e) {
         new CoreErrors($e);
      }
   }

   /**
    * Metoda inicializuje konfiguraci s konfiguračního souboru
    *
    */
   private function _initConfig() {
      self::$sysConfig = new Config(self::getAppWebDir() . DIRECTORY_SEPARATOR
         . self::MAIN_CONFIG_FILE);
   }

   /**
    * Metoda inicializuje šablonovací systém (SMARTY)
    */
   private function _initTemplate() {
      Template::factory();
      $this->coreTpl = new Template_Core();

      // filtry pro jazyky
      switch (Locale::getLang()) {
         case 'cs':
            //$this->template->load_filter('post', 'cztypo');
            //            $this->template->load_filter('output', 'czechtypo');
            //            $this->template->load_filter('output', 'trimwhitespace');
            break;
         default:
            break;
      }
      //		Pokud je debug tak vypnout kešování smarty
      if(self::$debugLevel >= 2){
         //         $this->template->force_compile = true;
         //         $this->template->clear_compiled_tpl();
      } else {
         //         $this->template->caching = true;
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
      function __autoload($classOrigName){
         $file = strtolower($classOrigName).'.class.php';
         $classL = strtolower($classOrigName);
         $pathDirs = explode('_', $classL);
         $moduleFile = $pathDirs[count($pathDirs)-1].'.class.php';
         unset ($pathDirs[count($pathDirs)-1]);
         $path = implode('/', $pathDirs);


         if(file_exists(AppCore::getAppWebDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR.$file)){
            require_once AppCore::getAppWebDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR.$file;
         } else if(file_exists(AppCore::getAppWebDir().AppCore::ENGINE_LIB_DIR
               .DIRECTORY_SEPARATOR.$classL.DIRECTORY_SEPARATOR.$file)){
            require_once AppCore::getAppWebDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR
            .$classL.DIRECTORY_SEPARATOR.$file;
         } else if(file_exists(AppCore::getAppWebDir().AppCore::ENGINE_LIB_DIR
               .DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$file)){
            require_once AppCore::getAppWebDir().AppCore::ENGINE_LIB_DIR
            .DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$file;
         } else if(file_exists(AppCore::getAppWebDir().AppCore::MODULES_DIR
               .DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$moduleFile)){
            require_once AppCore::getAppWebDir().AppCore::MODULES_DIR
            .DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$moduleFile;
         } else {
            echo _("Chybějící třída<br />");
            echo AppCore::getAppWebDir().AppCore::MODULES_DIR
            .DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR."<br />";
            echo $classOrigName." ".$file." module-file: ".$moduleFile;
//                        throw new BadFileException(sprintf(_("Nebyla nalezena třída %s soubor %s"),
//                              $classOrigName, $file));
            flush();
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
   }

   /**
    * Metoda inicializuje objekty pro práci s hláškami
    */
   private function _initMessagesAndErrors(){
      //		Vytvoření objektu pro práci se zprávami
      if(!UrlRequest::isAjaxRequest()){
         self::$messages = new Messages('session', 'messages', true);
      } else {
         self::$messages = new Messages('session', 'messages');
      }
      self::$userErrors = new Messages('session', 'errors');
   }

   /**
    * Medota pro inicializaci modulů
    */
   private function _initModules() {
      //		Načtení potřebných knihoven
      ModuleDirs::setWebDir(AppCore::MAIN_ENGINE_PATH); //TODO patří přepsat tak aby se to zadávalo jinde
      ModuleDirs::setWebDataDir(AppCore::sysConfig()->getOptionValue("data_dir"));
   }

   /**
    * Metoda ověřuje autorizaci přístupu
    */
   private function coreAuth() {
      self::$auth = new Auth(AppCore::getDbConnector());
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
         $this->coreTpl->addTplObj("MENU", $menu->template());
      } catch (Exception $e) {
         new CoreErrors($e);
      }
   }

   /**
    * Metoda přiřadí do šablony hlavní proměnné systému
    */
   public function assignMainVarsToTemplate() {
      //	Hlavni promene strany
      $this->coreTpl->pageTitle = self::sysConfig()->getOptionValue("web_name");
      //adresa k rootu webu
      $this->coreTpl->rootDir = self::getAppWebDir();
      $link = new Links();
      $this->coreTpl->setPVar("mainWebDir", UrlRequest::getBaseWebDir());
      $this->coreTpl->setPVar("imagesDir", Template::FACES_DIR.URL_SEPARATOR.Template::face()
         .URL_SEPARATOR.Template::IMAGES_DIR.URL_SEPARATOR);
      //$this->coreTpl->mainWebDir = UrlRequest::getBaseWebDir();
      //            $this->coreTpl->setVar("THIS_PAGE_LINK", (string)$link);
      // mapa webu
      $this->coreTpl->sitemapLink = (string)$link->clear(true)
      .UrlRequest::getSpecialPageRegexp(UrlRequest::SPECIAL_PAGE_SITEMAP);
      $this->coreTpl->mainLangImagesPath = self::sysConfig()
      ->getOptionValue('images_lang', 'dirs').URL_SEPARATOR;
      unset($link);
      //Přihlášení uživatele
      $this->coreTpl->userIsLogin = AppCore::getAuth()->isLogin();
      $this->coreTpl->userLoginUsername = AppCore::getAuth()->getUserName();
      //Verze enginu
      $this->coreTpl->engineVersion = self::sysConfig()->getOptionValue("engine_version");
      //Debugovaci mod
      if (self::$debugLevel > 1){
         $this->coreTpl->debugMode = true;
      }
      if (self::$debugLevel > 2){
         $this->coreTpl->showDebugConsole = true;
      }
      //      //		Přiřazení jazykového pole
      $this->coreTpl->setPVar("appLangsNames", Locale::getAppLangsNames());
      //      //		Vytvoření odkazů s jazyky
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


      $this->coreTpl->execTime = round($endTime-$this->_stratTime, 4);
      $this->coreTpl->countAllSqlQueries = Db::getCountQueries();
      //      //		Zvolení zobrazovaného média
      //      //		Medium pro tisk
      //      if(UrlRequest::getMediaType() == UrlRequest::MEDIA_TYPE_PRINT){
      ////         $this->template->display($faceFilePath."index-print.tpl");
      //         $this->template->display($faceFilePath."index-print.tpl");
      //      }
      //      //		Specielní media
      //      else if(file_exists($faceFilePath."index-".UrlRequest::getMediaType().".tpl")){
      ////         $this->template->display($faceFilePath."index-".UrlRequest::getMediaType().".tpl");
      //         $this->template->display($faceFilePath."index-".UrlRequest::getMediaType().".tpl");
      //      }
      //		Výchozí médium (www)
      //      else {
      //         $this->template->display($faceFilePath."index.tpl");
      $this->coreTpl->addTplFile("index.phtml", true);
      // render šablony
      $this->coreTpl->renderTemplate();
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
    */
   public function runModules() {
      $modulesModel = new Model_Module();
      $items = $modulesModel->getModules();
      //		procházení prvků stránky
      if($items != null){
         // pole s šablonama
         $modulesTemplates = array();

         //  Nastavení prováděné kategorie
         self::$currentCategory = array(Model_Category::COLUMN_CAT_LABEL => Category::getLabel()
            , Model_Category::COLUMN_CAT_ID => Category::getId());
         foreach ($items as $itemIndex => $item) {
            // kontrola  jestli nebyla vyvolána chabová stránka
            if(AppCore::isErrorPage()){
               break;
            }

            // vytvoření objektu sys systémem modulu
            $sysModule = new ModuleSys();

            // doplnění odkazů
            $sysModule->setLink(new Links());
            $sysModule->link()->category(Category::getLabel(), Category::getId());

            //	Vytvoření objektu pro práci s modulem
            $sysModule->setModule(new Module($item, $this->getModuleTables($item)));

            // nastavení locales
            $sysModule->setLocale(new Locale($sysModule->module()->getName()));

            // nasatvení article
            $sysModule->setArticle(new Article());

            //	vytvoření pole se skupinama a právama
            $userRights = array();
            foreach ($item as $collum => $value){
               if (substr($collum,0,strlen(Rights::RIGHTS_GROUPS_TABLE_PREFIX)) ==
                  Rights::RIGHTS_GROUPS_TABLE_PREFIX){
                  $userRights[substr($collum,strlen(Rights::RIGHTS_GROUPS_TABLE_PREFIX),
                        strlen($collum))]=$value;
               }
            }
            //	Vytvoření objektu pro přístup k právům modulu
            $sysModule->setRights(new Rights($userRights));
            // načtení souboru s akcemi modulu
            //				Vytvoření objektu akce
            $actionClassName = ucfirst($sysModule->module()->getName()).'_Action';
            //               if(!class_exists($actionClassName, false)){
            //                  if(file_exists('.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR
            //                        . $sysModule->module()->getName() . DIRECTORY_SEPARATOR . 'action.class.php')){
            ////                     throw new BadFileException(_("Nepodařilo se nahrát akci modulu ")
            ////                        . $sysModule->module()->getName(), 7);
            //                  require_once '.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR
            //                  . $sysModule->module()->getName() . DIRECTORY_SEPARATOR . 'action.class.php';
            //                  }
            //               }

            if(class_exists($actionClassName)){
               $sysModule->setAction(new $actionClassName($sysModule->module()));
            } else {
               $sysModule->setAction(new Action($sysModule->module()));
            }

            //				načtení souboru s cestami (routes) modulu
            //               if(!file_exists('.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR
            //                     . $sysModule->module()->getName() . DIRECTORY_SEPARATOR . 'routes.class.php')){
            //                  throw new BadFileException(_("Nepodařilo se nahrát cestu modul ") . $sysModule->module()->getName(), 8);
            //               }
            //               require_once '.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR
            //               . $sysModule->module()->getName() . DIRECTORY_SEPARATOR . 'routes.class.php';
            //				Vytvoření objektu cesty (routes)
            $routes = null;
            $routesClassName = ucfirst($sysModule->module()->getName()).'_Routes';
            if(class_exists($routesClassName)){
               $sysModule->setRoute(new $routesClassName());
            } else {
               $sysModule->setRoute(new Routes());
            }
            //				načtení souboru s kontrolerem modulu
            //               if(!file_exists('.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR
            //                     . $sysModule->module()->getName() . DIRECTORY_SEPARATOR . 'controler.class.php')){
            //                  throw new BadFileException(_("Nepodařilo se nahrát controler modulu ")
            //                     . $sysModule->module()->getName(), 9);
            //               }
            //               require_once '.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR
            //                  . $sysModule->module()->getName() . DIRECTORY_SEPARATOR . 'controler.class.php';

            //			načtení souboru s viewrem modulu
            //               if(!file_exists('.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR
            //                     . $sysModule->module()->getName() . DIRECTORY_SEPARATOR . 'view.class.php')){
            //                  throw new BadFileException(sprintf(_('Nepodařilo se nahrát soubor vieweru modulu "%s"'),
            //                        $sysModule->module()->getName()), 13);
            //               }
            //               require_once '.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR
            //                  . $sysModule->module()->getName() . DIRECTORY_SEPARATOR . 'view.class.php';

            //			Vytvoření objektu kontroleru
            try {
               //                throw new Exception("pokus",21);
               $controllerClassName = ucfirst($sysModule->module()->getName()).'_Controller';
               if(!class_exists($controllerClassName)){
                  throw new BadClassException(_("Nepodařilo se třídu controleru modulu ")
                     . $sysModule->module()->getName(), 10);
               }
               //					Vytvoření objektu kontroleru
               $controller = new $controllerClassName($sysModule);

               //				Vytvoření ubjektu UrlReqestu
               $urlRequest = new UrlRequest($sysModule->action(), $sysModule->route());
               //					Volba metody kontroluru podle urlrequestu
               $requestName = $urlRequest->choseController();
               $requestControllerName = $requestName.AppCore::MODULE_CONTROLLER_SUFIX;
               //					Spuštění kontroleru
               try {
                  $ctrlResult = null;
                  if(method_exists($controller, $requestControllerName)){
                     $ctrlResult = $controller->{$requestControllerName}();
                  } else {
                     //                  if(!method_exists($controller, strtolower(self::MODULE_MAIN_CONTROLLER_PREFIX)
                     //                        .self::MODULE_CONTROLLER_SUFIX)){
                     //                     throw new BadMethodCallException(sprintf(
                     //                           _('Action Controller "%s" v modulu "%s" nebyl nalezen'),
                     //                           strtolower(self::MODULE_MAIN_CONTROLLER_PREFIX).self::MODULE_CONTROLLER_SUFIX,
                     //                           $sysModule->module()->getName()), 11);
                     //                  }
                     $ctrlResult = $controller->mainController();
                     new CoreErrors(new BadMethodCallException(sprintf(
                              _('Action Controller "%s" v modulu "%s" nebyl nalezen'),
                              $requestControllerName, $sysModule->module()->getName()), 12));
                  }
               } catch (ModuleException $e) {
                  new CoreErrors($e);
               } catch (Exception $e) {
                  new CoreErrors($e);
               }
               //					Donastavení šablon
               //               $template->setModule($module);
               //	Spuštění viewru pokud proběhl kontroler v pořádku
               // Není-li v kontroleru přiřazen výstup
               //               if($ctrlResult === null){
               //               $ctrlResult = true;
               //               }
               //	Spuštění pohledu
               //               if($ctrlResult){
               //                  if(!method_exists($controller, 'runView')){
               //                     throw new BadMethodCallException(_("Action Controller runView v modulu ")
               //                        . $sysModule->module()->getName()._(" nebyl nalezen"), 14);
               //                  }
               try {
                  $controller->runView($requestName.AppCore::MODULE_VIEWER_SUFIX);
                  //               } else {
                  //                  throw new BadMethodCallException(_('Controler modulu "')
                  //                     . $sysModule->module()->getName()._('" nebyl korektně proveden'), 15);
                  //               }
                  //	Uložení šablony a proměných do hlavní šablony

               } catch (ModuleException $e) {
                  new CoreErrors($e);
               } catch (Exception $e) {
                  new CoreErrors($e);
               }
               array_push($modulesTemplates, $controller->_getTemplateObj());
               //	odstranění proměných
               unset($controller);
            } catch (BadClassException $e){
               new CoreErrors($e);
            } catch (BadMethodCallException $e){
               new CoreErrors($e);
            } catch (CoreException $e){
               new CoreErrors($e);
            } catch (Exception $e){
               new CoreErrors($e);
            }
         }
         $this->coreTpl->modules = $modulesTemplates;
         unset ($modulesTemplates);
      } else {
         if(new Links() == new Links(true)){
            throw new UnderflowException(_("Nepodařilo se nahrát prvky kategorie z databáze."), 16);
         } else {
            $redir = new Links(true);
            $redir->category()->action()->article()->rmParam()->reload();
         }
      }
   }

   /**
    * Metoda inicializuje a spustí panel
    * @param string $side -- jaký panel je spuštěn (left, right, bottom, top, ...)
    * @todo dodělat implementaci ostatních pozic panelů
    */
   public function runPanel($side){
      //	Rozdělenní, který panel je zpracován
      $panelSideUpper = strtoupper($side);
      $panelSideLower = strtolower($side);
      //	Zapnutí panelu

      $panelSides = array();
      $panelSides[$panelSideUpper] = true;
      if(!is_array($this->coreTpl->panel)){
         $this->coreTpl->panel = array();
      }
      $this->coreTpl->panel  = array_merge($this->coreTpl->panel, $panelSides);

      // Načtení panelů
      $panelModel = new Model_Panel();
      $panelData = $panelModel->getPanel($side);
      if(!empty($panelData)){
         $panelsTempaltes = array();
         foreach ($panelData as $panel) {
            // Nastavení prováděné kategorie
            self::$currentCategory = $category
            = array(Model_Category::COLUMN_CAT_LABEL => $panel->{Model_Category::COLUMN_CAT_LABEL},
               Model_Category::COLUMN_CAT_ID => $panel->{Model_Category::COLUMN_CAT_ID});
            // Vytvoření tabulek
            $tableIndex = 1; $moduleDbTables = array();
            $objectName=self::MODULE_DBTABLES_PREFIX.$tableIndex;
            while (isset($panel->$objectName) AND ($panel->$objectName != null)){
               $moduleDbTables[$tableIndex] = $panel->$objectName;
               $tableIndex++;
               $objectName=self::MODULE_DBTABLES_PREFIX.$tableIndex;
            }
            // systemový objekt
            $panelSys = new ModuleSys();

            // Příprava modulu
            $panelSys->setModule(new Module($panel, $moduleDbTables));

            // nastavení locales
            $panelSys->setLocale(new Locale($panelSys->module()->getName()));

            $panelClassName = ucfirst($panelSys->module()->getName()).self::MODULE_PANEL_CLASS_SUFIX;
            // Spuštění panelu
            try {
               //	vytvoření pole se skupinama a právama
               $userRights = array();
               foreach ($panel as $collum => $value){
                  if (substr($collum,0,strlen(Rights::RIGHTS_GROUPS_TABLE_PREFIX)) == Rights::RIGHTS_GROUPS_TABLE_PREFIX){
                     $userRights[substr($collum,strlen(Rights::RIGHTS_GROUPS_TABLE_PREFIX), strlen($collum))]=$value;
                  }
               }
               //	Vytvoření objektu pro přístup k právům modulu
               $panelSys->setRights(new Rights($userRights));

               // objekt odkazu
               $link = new Links(true);
               $link->category($panel->{Model_Category::COLUMN_CAT_LABEL}, $panel->{Model_Category::COLUMN_CAT_ID});
               $panelSys->setLink($link);

               //	CONTROLLER PANELU
               $panelCtrl = new $panelClassName($panelSys);

               $panelCtrl->{self::MODULE_PANEL_CONTROLLER}();

               $panelCtrl->{self::MODULE_PANEL_VIEWER}();
               array_push($panelsTempaltes, $panelCtrl->_getTemplateObj());
            }  catch (ModuleException $e) {
               new CoreErrors($e);
            } catch (BadFileException $e) {
               new CoreErrors($e);
            } catch (BadMethodCallException $e) {
               new CoreErrors($e);
            } catch (BadClassExceptio $e) {
               new CoreErrors($e);
            } catch (Exception $e) {
               new CoreErrors($e);
            }
         }
         $this->coreTpl->{$panelSideLower."Panel"} = $panelsTempaltes;
         unset ($panelsTempaltes);
      }
   }

   /**
    * metoda vybere, která kategorie je vybrána a uloží je di objektu kategorie
    */
   public function selectCategory() {
      Category::factory(AppCore::getAuth());
      $this->coreTpl->categoryTitle = Category::getLabel();
      $this->coreTpl->categoryId = Category::getId();
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
      //      $this->coreTpl->setVar("MESSAGES_EMPTY", self::getInfoMessages()->isEmpty());
      $this->coreTpl->moduleErrors = self::getUserErrors()->getMessages();
      //      $this->coreTpl->setVar("MODULE_ERRORS_EMPTY", self::getUserErrors()->isEmpty());
   }

   /**
    * Metoda vytváří sitemapu a odesílá ji na výstup
    */
   public function runSitemap() {
      $sitemapItems = new SitemapModel();
      $sitemapItems = $sitemapItems->getItems();
      // vyttvoření hlavní kategorie
      $sitemap = new SiteMap(new Links(true, true), AppCore::sysConfig()
         ->getOptionValue('sitemap_periode'), 1.0);
      $sitemap->run();
      unset ($sitemap);
      // procházení kategoríí na vytváření sitemapy
      if($sitemapItems != null){
         foreach ($sitemapItems as $itemIndex => $item) {
            //				Vytvoření objektu pro práci s modulem
            $module = new Module($item, $this->getModuleTables($item));
            AppCore::setSelectedModule($module);
            $link = new Links(true);
            $link = $link->category($item->{Category::COLUMN_CAT_LABEL_ORIG},
               $item->{Category::COLUMN_CAT_ID});
            $moduleName = ucfirst($module->getName());
            $moduleClass = $moduleName.self::MODULE_SITEMAP_SUFIX_CLASS;
            //				Pokud existuje soubor tak jej načteme
            if(file_exists($module->getDir()->getMainDir(false).strtolower(self::MODULE_SITEMAP_SUFIX_CLASS).'.class.php')){
               include_once ($module->getDir()->getMainDir(false).strtolower(self::MODULE_SITEMAP_SUFIX_CLASS).'.class.php');
               if(class_exists($moduleClass)){
                  $sitemap = new $moduleClass($link, $item->{SitemapModel::COLUMN_SITEMAP_FREQUENCY},
                     (float)$item->{SitemapModel::COLUMN_SITEMAP_PRIORITY});
                  // spuštění sitemapy
                  $sitemap->run();
                  unset($sitemap);
               }
            } else {
               //throw new Exception(_('Chybí soubor pro zpracování sitemapy'));
            }
            // vyprázdnění modulu
            AppCore::setSelectedModule();
         }
         //	Vygenerování mapy	Podle vyhledávače a ukončení scriptu
         SiteMap::generateMap(UrlRequest::getSupportedServicesName());
         exit ();
      }
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
      $this->coreTpl->specialPage = true;
      $this->coreTpl->specialPageName = UrlRequest::getSpecialPage();
      switch (UrlRequest::getSpecialPage()) {
         case UrlRequest::SPECIAL_PAGE_SEARCH:
            $this->runSearchPage();
            break;
         case UrlRequest::SPECIAL_PAGE_SITEMAP:
            $this->runSitemapPage();
            break;
         default:
            $this->setErrorPage();
            break;
      }
   }

   /**
    * Metoda pro spuštění hledání
    */
   public function runSearchPage() {
      $searchM = new SearchModel();
      $modules = $searchM->getModules();
      $itemsArray = $searchM->getItems();
      if($modules != null){
         foreach ($modules as $itemIndex => $item) {
            //				Vytvoření objektu pro práci s modulem
            $module = new Module($item, $this->getModuleTables($item));
            AppCore::setSelectedModule($module);
            $moduleName = ucfirst($module->getName());
            $moduleClass = $moduleName.self::MODULE_SEARCH_SUFIX_CLASS;
            //				Pokud existuje soubor tak jej načteme
            if(file_exists($module->getDir()->getMainDir(false).strtolower(self::MODULE_SEARCH_SUFIX_CLASS).'.class.php')){
               include_once ($module->getDir()->getMainDir(false).strtolower(self::MODULE_SEARCH_SUFIX_CLASS).'.class.php');
               if(class_exists($moduleClass)){
                  $sitemap = new $moduleClass($itemsArray[$module->getIdModule()]);
                  // spuštění hledání v modulu
                  $sitemap->runSearch();
                  unset($sitemap);
               }
            }
            // vyprázdnění modulu
            AppCore::setSelectedModule();
         }
         $this->coreTpl->searchResults = Search::getResults();
         $this->coreTpl->searchResultsCount = Search::getNumResults();
      }
   }

   /**
    * Metoda spouští generování stránky s mapou webu
    */
   public function runSitemapPage() {
      $sitemapItems = new SitemapModel();
      $sitemapItems = $sitemapItems->getItemsOrderBySections();
      // procházení kategoríí na vytváření sitemapy
      $sitemapArray = array();
      if($sitemapItems != null){
         foreach ($sitemapItems as $itemIndex => $item) {
            $idSection = $item->{SectionsModel::COLUMN_SEC_ID};
            if(!isset ($sitemapArray[$idSection])){
               $sitemapArray[$idSection] =
               array('name' =>$item->{SectionsModel::COLUMN_SEC_LABEL},
                     'categories' => array());
            }
            $idCategory = $item->{Category::COLUMN_CAT_ID};
            //				Vytvoření objektu pro práci s modulem
            $module = new Module($item, $this->getModuleTables($item));
            AppCore::setSelectedModule($module);
            $link = new Links(true);
            $link = $link->category($item->{Category::COLUMN_CAT_LABEL}, $idCategory);
            //            vytvoření pole s kategorií
            if(!isset ($sitemapArray[$idSection]['categories'][$idCategory])){
               $sitemapArray[$idSection]['categories'][$idCategory] = array(
                  'name' => $item->{Category::COLUMN_CAT_LABEL},
                  'url' => (string)$link,
                  'results' => array());
            }
            $moduleName = ucfirst($module->getName());
            $moduleClass = $moduleName.self::MODULE_SITEMAP_SUFIX_CLASS;
            //				Pokud existuje soubor tak jej načteme
            if(file_exists($module->getDir()->getMainDir(false).strtolower(self::MODULE_SITEMAP_SUFIX_CLASS).'.class.php')){
               include_once ($module->getDir()->getMainDir(false).strtolower(self::MODULE_SITEMAP_SUFIX_CLASS).'.class.php');
               if(class_exists($moduleClass)){
                  $sitemap = new $moduleClass($link, null,null);
                  // spuštění sitemapy
                  $sitemap->run();
                  $sitemapArray[$idSection]['categories'][$idCategory]['results'] =
                  array_merge($sitemapArray[$idSection]['categories'][$idCategory]['results'],
                     $sitemap->getCurrentMapArray());
                  unset($sitemap);
               }
            }
            // vyprázdnění modulu
            AppCore::setSelectedModule();
         }
      }
      $this->coreTpl->sitemapPages = $sitemapArray;
   }

   /**
    * Metoda načte soubor se specialními vlastnostmi přenesenými do šablony,
    * které jsou jednotné pro celý web
    */
   private function initialWebSettings() {
      $fileName = 'initial'.ucfirst(UrlRequest::getMediaType()).'.php';
      if(file_exists(self::MAIN_ENGINE_PATH.self::MODULES_DIR.DIRECTORY_SEPARATOR.$fileName)){
         require self::MAIN_ENGINE_PATH.self::MODULES_DIR.DIRECTORY_SEPARATOR.$fileName;
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
      // pokud je spuštěna služba Supported Services (Eplugin, JsPlugin, Sitemap, RSS, Atom)
      if(UrlRequest::isSupportedServices()){
         //		Pokud se načítá statická část epluginu
         if(UrlRequest::getSupportedServicesType() == UrlRequest::SUPPORTSERVICES_EPLUGIN_NAME){
            //					$epluginName = ucfirst(Eplugin::getSelEpluginName());
            $epluginName = ucfirst(UrlRequest::getSupportedServicesName());
            $epluginWithOutEplugin = $epluginName.'Eplugin';
            /**
             * @todo co to sakra je? proč je to tu dvakrát? a kde je předání parametrů?
             */
            if(class_exists($epluginName)){
               $eplugin = new $epluginName();
               $eplugin->initRunOnlyEplugin();
               return true;
            } else if(class_exists($epluginWithOutEplugin)){
               $eplugin = new $epluginWithOutEplugin();
               $eplugin->initRunOnlyEplugin();
               return true;
            } else {
               new CoreException(_('Požadovaný eplugin nebyl nalezen'), 21);
            }
            unset($epluginName);
         }
         //		Pokud se načítá statická část jspluginu
         else if(UrlRequest::getSupportedServicesType() == UrlRequest::SUPPORTSERVICES_JSPLUGIN_NAME){
            //					$jspluginName = ucfirst(JsPlugin::getSelJspluginName());
            $jspluginName = ucfirst(UrlRequest::getSupportedServicesName());
            if(class_exists($jspluginName)){
               $jsplugin = new $jspluginName();
               return true;
            } else {
               new CoreException(_('Požadovaný jsplugin nebyl nalezen'), 22);
            }
            unset($jspluginName);
         }
         //		Pokud se načítá statická část sitemap
         else if(UrlRequest::getSupportedServicesType() == UrlRequest::SUPPORTSERVICES_SITEMAP_NAME){
            $this->runSitemap();
         }
      }
      // pokud je spuštěn ajax požadavek
      else if(UrlRequest::isAjaxRequest()){
         try {
            if(UrlRequest::getAjaxType() == Ajax_Link::AJAX_EPLUGIN_NAME){
               $epluginName = UrlRequest::getAjaxName();
               //echo $epluginName;
               //exit ();
               if(!class_exists($epluginName)) {
                  throw new BadClassException(_('Neplatný typ Epluginu'), 23);
               }
               $eplugin = new $epluginName();
               //            $ajaxObj = new Ajax(UrlRequest::getAjaxFileParams());
               $ajaxObj = new Ajax(UrlRequest::getAjaxFileParams());

               if(!method_exists($eplugin, $ajaxObj->getAjaxMetod())){
                  throw new BadClassException(_('Neexistující ajax metoda Epluginu'), 24);
               }

               $eplugin->{$ajaxObj->getAjaxMetod()}($ajaxObj);

            } else if(UrlRequest::getAjaxType() == Ajax_Link::AJAX_MODULE_NAME){

            }
         } catch (Exception $e) {
            new CoreErrors($e);
         }

         if(!AppCore::getInfoMessages()->isEmpty()){
            echo AppCore::getInfoMessages();
         }

         if(!AppCore::getUserErrors()->isEmpty()){
            echo AppCore::getUserErrors();
         }

         if(!CoreErrors::isEmpty()){
            echo CoreErrors::getLastError();
         }
         exit();
      }
      // pokud je zpracovávána normální aplikace a její moduly
      else {
         switch (UrlRequest::getMediaType()) {
            // Volba typu média
            case UrlRequest::MEDIA_TYPE_WWW:
               default:
                  //				Výchozí je zobrazena stránka =======================================
                  //				Příprava šablony
                  //				inicializace šablonovacího systému
                  $this->_initTemplate();
                  //nastavení vybrané kategorie
                  $this->selectCategory();
                  // Globální inicializace proměných do šablony
                  $this->initialWebSettings();
                  //vytvoření hlavního menu
                  $this->createMainMenu();

                  if(UrlRequest::isSpecialPage()){
                     $this->runSpecialPage();
                  }
                  // Pokud není chyba spustíme moduly
                  if(!AppCore::isErrorPage() AND !UrlRequest::isSpecialPage()){
                     //		spuštění modulů
                     $this->runModules();
                  }

                  // =========	spuštění panelů
                  //		Levý
                  if(Category::isLeftPanel()){
                     $this->runPanel('left');
                  }
                  //		Pravý
                  if(Category::isRightPanel()){
                     $this->runPanel('right');
                  }
                  // pokud se v aplikaci vyskitla chybová stránka, spustíme ji
                  if(AppCore::isErrorPage()){
                     $this->runErrorPage();
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
               }
            }
         }
      }
      ?>