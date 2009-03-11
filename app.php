<?php
/**
 * Vypecky Engine.
 * Hlavní třída aplikace - singleton
 * Obsluhuje celou aplikaci a její komponenty.
 *
 * @copyright  Copyright (c) 2008 Jakub Matas
 * @version    $Id: $ VVE3.9.1 $Revision: $
 * @author     $Author: jakub $ $Date: 2008-12-30 01:35:31 +0100 (Tue, 30 Dec 2008) $
 *             $LastChangedBy: jakub $ $LastChangedDate: 2008-12-30 01:35:31 +0100 (Tue, 30 Dec 2008) $
 * @abstract 	Hlavní třída aplikace(Singleton)
 * @license    GNU General Public License v. 2 viz. Docs/license.txt
 *
 */

class AppCore {
    /**
     * Výchozí cestak enginu
     * @var string
     */
   const MAIN_ENGINE_PATH = './';

    /**
     * Obsahuje hlavní soubor aplikace
     * @var string
     */
   const APP_MAIN_FILE = 'index.php';

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
     *
     * Adresář s validátory
     * @var string
     */
   const ENGINE_VALIDATORS_DIR = 'Validators';

    /**
     * Adresář s Modely enginu
     * @var string
     */
   const ENGINE_MODELS_DIR = 'models';

    /**
     * Adresář s pluginy filesystému
     * @var string
     */
   const ENGINE_FILESYSTEM_DIR = 'Filesystem';

    /**
     * Adresář s ostatními pluginy
     * @var string
     */
   const ENGINE_PLUGINS_DIR = 'Plugins';

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
     * objekt právě vybraného modulu
     * @var Module
     */
   private static $selectedModule = null;

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

      //	nastavení hlavního adresáře aplikace
        /*
         * @todo prověřit, protože né vždy se správně přiřadí cesta, pravděpodobně BUG php
         */
      //$direName = dirname(__FILE__); // OLD version + dává někdy špatný výsledek
      //$realPath = realpath($direName); // OLD version + dává někdy špatný výsledek
      $realPath = dirname(__FILE__); // ověřit v php 5.3.0 lze použít __DIR__

      //        echo '$direName: '.$direName."<br>";
      //        echo '$realPath: '.$realPath."<br>";

      $this->setAppMainDir($realPath);

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

      //inicializace lokalizace
      $this->_initLocale();

      //		inicializace URL
      $this->_initUrlRequest();

      //		inicializace sessions
      $this->_initSessions();



      //		inicializace db konektoru
      $this->_initDbConnector();

      //		Inicializace chybových hlášek
      $this->_initMessagesAndErrors();

      //Spuštění jádra aplikace
      $this->runApp();

      //		Vypsání chyb na výstup
//      $this->coreErrors->getErrorsToStdIO();
   }

    /**
     * Třída je singleton
     * není povolen clone
     */
   private function __clone() {
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
   public static function getInstance()
   {
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
     * @param boolean -- jestli se má vrátit celá adresář faces (true)
     * @return string -- adresář zvoleného vzhledu
     */
   public static function getTepmlateFaceDir($fullDir = true, $withFacesDir = true) {
      if($fullDir){
         return self::$_appWebDir.DIRECTORY_SEPARATOR.self::FACES_DIR.DIRECTORY_SEPARATOR.self::$templateFaceDir.DIRECTORY_SEPARATOR;

      } else {
         if($withFacesDir){
            return self::FACES_DIR.URL_SEPARATOR.self::$templateFaceDir.URL_SEPARATOR;
         } else {
            return self::$templateFaceDir.URL_SEPARATOR;
         }
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
         return self::FACES_DIR.URL_SEPARATOR.self::$templateDefaultFaceDir.URL_SEPARATOR;
      }
   }

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
   public static function getSelectedModule() {
      return self::$selectedModule;
   }

     /**
      * Metoda nastavuje objekt aktuálního modulu
      * @param Module $module -- (option) objekt vybraného modulu
      */
   public static function setSelectedModule($module = null) {
      self::$selectedModule = $module;
   }

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

     /*
      * PRIVÁTNÍ METODY
      */

    /**
     * Metoda pro vytvoření objektu pro obsluhu chyb jádra
     * (CoreErrors)
     * @todo -- optimalizovat pro lepší práci
     */
   private function _initCoreErros() {
//      $this->coreErrors = new Errors();
//      CoreException::_setErrorsHandler($this->coreErrors);
   }

    /**
     * Metoda inicializuje připojení k databázi
     */
   private function _initDbConnector() {

      require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'db' . DIRECTORY_SEPARATOR. 'db.class.php');

      try {
         self::$dbConnector = Db::factory(self::sysConfig()->getOptionValue("dbhandler", self::MAIN_CONFIG_DB_SECTION),
            self::sysConfig()->getOptionValue("dbserver", self::MAIN_CONFIG_DB_SECTION),
            self::sysConfig()->getOptionValue("dbuser", self::MAIN_CONFIG_DB_SECTION),
            self::sysConfig()->getOptionValue("dbpasswd", self::MAIN_CONFIG_DB_SECTION),
            self::sysConfig()->getOptionValue("dbname", self::MAIN_CONFIG_DB_SECTION),
            self::sysConfig()->getOptionValue("tbprefix", self::MAIN_CONFIG_DB_SECTION));

//         if(self::$dbConnector == false){
//            //            throw new CoreException(self::sysConfig()->getOptionValue("dbhandler", self::MAIN_CONFIG_DB_SECTION)._(" Databázový engine nebyl implementován"), 1);
//         }
      } catch (UnexpectedValueException $e) {
         new CoreErrors($e);
      }

   }

    /**
     * Metoda inicializuje požadavky v URL
     */
   private function _initUrlRequest() {
      UrlRequest::factory();
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
      self::$sysConfig = new Config(self::getAppWebDir() . DIRECTORY_SEPARATOR . self::MAIN_CONFIG_FILE, $this->coreErrors);
   }

    /**
     * Metoda nastavuje locales a gettext pro překlady
     */
   private function _initLocale() {
      Locale::factory();
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

   }

    /**
     * Metoda načte potřebné knihovny
     */
   private function _loadLibraries() {
        /**
         * Funkce slouží pro automatické načítání potřebných tříd
         * @param string -- název třídy
         */
      function __autoload($classOrigName){
         //TODO dodělat kontroly, tak ať to vyhazuje přesnější chbové hlášky
         //		Zmenšení na malá písmena
         $className = strtolower($classOrigName);

         $epluginFile = $className;
         //pokud je eplugin odstraníme jej z názvu
         if(strpos($className, 'eplugin') !== false AND $className != 'eplugin'){
            $epluginFile = str_replace('eplugin', '', $className);
         }

         //je načítána hlavní knihovna
         if(file_exists('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. $className . '.class.php')){
            require ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. $className . '.class.php');
         }
         //je načítán e-plugin
         else if(file_exists('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_EPLUINS_DIR . DIRECTORY_SEPARATOR . $epluginFile . '.class.php')) {
            require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_EPLUINS_DIR . DIRECTORY_SEPARATOR . $epluginFile . '.class.php');
         }
         //			Je-li načítán JsPlugin
         else if(file_exists('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_JSPLUINS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php')) {
            require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_JSPLUINS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php');
         }
         //			Je-li načítán model
         else if(file_exists('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_MODELS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php')) {
            require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_MODELS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php');
         }
         //			Je-li načítán helper
         else if(file_exists('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_HELPERS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php')) {
            require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_HELPERS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php');
         }
         //			Je-li načítán validátor
         else if(file_exists('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_VALIDATORS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php')) {
            require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_VALIDATORS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php');
         }
         //			Je-li načítán filesystem plugin
         else if(file_exists('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_FILESYSTEM_DIR . DIRECTORY_SEPARATOR . $className . '.class.php')) {
            require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_FILESYSTEM_DIR . DIRECTORY_SEPARATOR . $className . '.class.php');
         }
         //			Je-li načítán jíný plugin
         else if(file_exists('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_PLUGINS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php')) {
            require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_PLUGINS_DIR . DIRECTORY_SEPARATOR . $className . '.class.php');
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
      //		knihovny pro práci s chybami
      require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'Exceptions' . DIRECTORY_SEPARATOR . 'coreException.class.php');
      require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'Exceptions' . DIRECTORY_SEPARATOR . 'dbException.class.php');
      require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'Exceptions' . DIRECTORY_SEPARATOR . 'badClassException.class.php');
   }

    /**
     * Metoda inicializuje objekty pro práci s hláškami
     *
     */
   private function _initMessagesAndErrors()
   {
      //		Vytvoření objektu pro práci se zprávami
      self::$messages = new Messages('session', 'messages', true);
      self::$userErrors = new Messages('session', 'errors');
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

    /**
     * Metoda inicializuje typ média pro zobrazení
     */
   //	private function _initMediaType() {
   //		if(UrlRequest::getMediaType() != null){
   ////			AppCore::$mediaType = $_GET[self::MEDIA_URL_PARAM_TYPE];
   //			AppCore::$mediaType = $_GET[Links::GET_MEDIA];
   //		}
   //	}

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
   private static function coreAuth() {
      self::$auth = new Auth(AppCore::getDbConnector());
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
         $this->template->append($varName, $value, true);
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
         $menu = new Menu(self::$dbConnector);

         $menu->controller();

         $menu->view();

         $this->assginTplObjToTpl($menu->getTemplate(), MainMenu::TPL_ARRAY_NAME);

      } else {
         new CoreException(_("Třída s hlavním menu neexistuje"), 1);
      }

      //		Přiřazení souboru s šablonou menu podle zvoleéhoí vzhledu
      //		vybraný vzhled šablony //TODO přesunout do třídy pro práci s menu

      $this->assignVarToTpl('MAIN_MENU_TEMPLATE_FILE', 'menu.tpl');
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
      $this->coreTpl->addVar("MAIN_PAGE_TITLE", self::sysConfig()->getOptionValue("web_name"));

      //adresa k rootu webu
      $this->coreTpl->addVar("MAIN_ROOT_DIR", self::getAppWebDir());

      $link = new Links();
      $this->coreTpl->addVar("MAIN_WEB_DIR", UrlRequest::getBaseWebDir());
      $this->coreTpl->addVar("THIS_PAGE_LINK", $link);

      $this->coreTpl->addVar("MAIN_LANG_IMAGES_PATH", self::sysConfig()->getOptionValue('images_lang', 'dirs').URL_SEPARATOR);
      $this->coreTpl->addVar("MAIN_CURRENT_FACE_PATH", self::getTepmlateFaceDir(false));
      $this->coreTpl->addVar("MAIN_CURRENT_FACE_IMAGES_PATH", self::getTepmlateFaceDir(false).self::sysConfig()->getOptionValue('images', 'dirs').URL_SEPARATOR);
      unset($link);

      //Přihlášení uživatele
      $this->coreTpl->addVar("USER_IS_LOGIN",  AppCore::getAuth()->isLogin());
      $this->coreTpl->addVar("NOT_LOGIN_USER_NAME",_("Nepřihlášen"));
      $this->coreTpl->addVar("LOGIN_USER_NAME",_("Přihlášen"));
      $this->coreTpl->addVar("USER_LOGIN_USERNAME", AppCore::getAuth()->getUserName());

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
      $this->coreTpl->addVar('APP_LANG' ,Locale::getLang());
   }

    /**
     * metoda vyrenderuje šablonu
     */
   public function renderTemplate() {
      //		načtení doby zpracovávání aplikace
      List ($usec, $sec) = Explode (' ', microtime());
      $endTime = ((float)$sec + (float)$usec);
      $this->coreTpl->addVar("MAIN_EXEC_TIME", round($endTime-$this->_stratTime, 4));
      $this->coreTpl->addVar("COUNT_ALL_SQL_QUERY", Db::getCountQueries());


      //		Přiřazení popisků do šablony
      $this->assignEngineLabelsToTpl();

      //		Přiřazení javascriptů a stylů
      $this->assignVarToTpl("STYLESHEETS", Template::getStylesheets());
      $this->assignVarToTpl("JAVASCRIPTS", Template::getJavaScripts());
      $this->assignVarToTpl("ON_LOAD_JS_FUNCTIONS", Template::getJsOnLoad());

      //		Přiřazení proměných z hlavní šablony
      $this->assginTplObjToTpl($this->coreTpl);


        /**
         * @todo dořešit při neexistenci ostatní typů medií
         */
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
      //		Medium pro tisk
      if(UrlRequest::getMediaType() == UrlRequest::MEDIA_TYPE_PRINT){
         $this->template->display($faceFilePath."index-print.tpl");
      }
      //		Specielní media
      else if(file_exists($faceFilePath."index-".UrlRequest::getMediaType().".tpl")){
         $this->template->display($faceFilePath."index-".UrlRequest::getMediaType().".tpl");
      }
      //		Výchozí médium (www)
      else {
         $this->template->display($faceFilePath."index.tpl");
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
      $modulesModel = new ModuleModel();
      $items = $modulesModel->getModules();

      //		procházení prvků stránky
      if($items != null){
         //  Nastavení prováděné kategorie
         self::$currentCategory = array(Category::COLUMN_CAT_LABEL => Category::getLabel()
            , Category::COLUMN_CAT_ID => Category::getId());
         foreach ($items as $itemIndex => $item) {
            //			Vytvoření objektu šablony
            $template = new Template();

            //				Vytvoření objektu pro práci s modulem
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
            $moduleRights = new Rights($userRights);

            try {
               // načtení souboru s akcemi modulu
               if(!file_exists('.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'action.class.php')){
                  throw new BadFileException(_("Nepodařilo se nahrát akci modulu ") . $module->getName(), 12);
               }
               include_once '.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'action.class.php';

               //				Vytvoření objektu akce
               $action = null;
               $actionClassName = ucfirst($module->getName()).'Action';
               if(class_exists($actionClassName)){
                  $action = new $actionClassName();
               } else {
                  $action = new Action();
               }

               //				načtení souboru s cestami (routes) modulu
               if(!file_exists('.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'routes.class.php')){
                  throw new BadFileException(_("Nepodařilo se nahrát cestu modul ") . $module->getName(), 10);
               }
               include_once '.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'routes.class.php';

               //				Vytvoření objektu cesty (routes)
               $routes = null;
               $routesClassName = ucfirst($module->getName()).'Routes';
               if(class_exists($routesClassName)){
                  $routes = new $routesClassName();
               } else {
                  $routes = new Routes();
               }

               //				Vytvoření ubjektu UrlReqestu
               $urlRequest = new UrlRequest($action, $routes);

               //				načtení souboru s kontrolerem modulu
               if(!file_exists('.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'controler.class.php')){
                  throw new BadFileException(_("Nepodařilo se nahrát controler modulu ") . $module->getName(), 5);
               }
               require_once '.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'controler.class.php';

               //			Vytvoření objektu kontroleru
               $controllerClassName = ucfirst($module->getName()).'Controller';
               if(!class_exists($controllerClassName)){
                  throw new BadClassException(_("Nepodařilo se vytvořit objekt controleru modulu ") . $module->getName(), 6);
               }
               //					Vytvoření objektu kontroleru
               $controller = new $controllerClassName($action, $routes, $moduleRights);

               //					Volba metody kontroluru podle urlrequestu
               $requestName = $urlRequest->choseController();
               $requestControllerName = $requestName.AppCore::MODULE_CONTROLLER_SUFIX;

               //					Příprava a nastavení použití překladu
               Locale::bindTextDomain($module->getName());

               //					Zvolení překladu na modul
               Locale::switchToModuleTexts($module->getName());

               //					Spuštění kontroleru
               if(method_exists($controller, $requestControllerName)){
                  $ctrlResult = $controller->$requestControllerName();
               } else {
                  if(!method_exists($controller, strtolower(self::MODULE_MAIN_CONTROLLER_PREFIX).self::MODULE_CONTROLLER_SUFIX)){
                     throw new BadMethodCallException(_("Action Controller ")
                           .strtolower(self::MODULE_MAIN_CONTROLLER_PREFIX).self::MODULE_CONTROLLER_SUFIX
                        ._(" v modulu ") . $module->getName()._(" nebyl nalezen"), 11);
                  }

                  $ctrlResult = $controller->mainController();
                  //	Vrácení překladu na engine
                  Locale::switchToEngineTexts();
                  new CoreErrors(new BadMethodCallException(_("Action Controller ").$requestControllerName._(" v modulu ") . $module->getName()._(" nebyl nalezen"), 11));
               }
               Locale::switchToEngineTexts();

               //			načtení souboru s viewrem modulu
               if(!file_exists('.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'view.class.php')){
                  throw new BadFileException(_("Nepodařilo se nahrát viewer modulu ") . $module->getName(), 7);
               }
               require_once '.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'view.class.php';

               //					Donastavení šablon
               $template->setModule($module);

               //					Spuštění viewru
               //					Pokud proběhl kontroler v pořádku
               //Není-li v kontroleru přiřazen výstup
               if($ctrlResult === null){
                  $ctrlResult = true;
               }

               //	Spuštění pohledu
               if($ctrlResult){
                  if(!method_exists($controller, 'runView')){
                     throw new BadMethodCallException(_("Action Controller runView v modulu ")
                        . $module->getName()._(" nebyl nalezen"), 11);
                  }
                  //					Zvolení překladu na modul
                  Locale::switchToModuleTexts($module->getName());
                  $controller->runView($template, $requestName.AppCore::MODULE_VIEWER_SUFIX);
               } else {
                  Locale::switchToEngineTexts();
                  new CoreErrors(new BadMethodCallException(_('Controler modulu "') . $module->getName()
                        ._('" nebyl korektně proveden'), 21));
               }

               //					Uložení šablony a proměných do hlavní šablony
               $this->assginTplObjToTpl($template, 'MODULES_TEMPLATES');

               //	Vrácení překladu na engine
               Locale::switchToEngineTexts();
               unset($template);
               unset($controller);
               $isModuleControlerRun = true;
            } catch (Exception $e) {
               new CoreErrors($e);
            } catch (BadClassException $e){
               new CoreErrors($e);
            } catch (BadFileException $e){
               new CoreErrors($e);
            } catch (BadMethodCallException $e){
               new CoreErrors($e);
            }
        //				Vrácení překladu na engine pro jistotu
            Locale::switchToEngineTexts();

            //				odstranění proměných
            unset($module);
            self::$selectedModule = null;
            unset($model);
            unset($action);
            unset($routes);
            unset($controller);
            unset($actionCtrl);
         }
      } else {
         if(new Links() == new Links(true)){
            throw new UnderflowException(_("Nepodařilo se nahrát prvky kategorie z databáze."), 9);
         } else {
            $redir = new Links(true);
            $redir->category()->action()->article()->rmParam()->reload();
         }
      }
   }

    /**
     * Metoda inicializuje a spustí levý panel
     */
   public function runPanel($side){
      //	Rozdělenní, který panel je zpracován
      $panelSideUpper = strtoupper($side);
      $panelSideLower = strtolower($side);

      //	Zapnutí panelu
      $this->assignVarToTpl($panelSideUpper."_PANEL", true);

      // Načtení panelů
      $panelModel = new PanelModel();
      $panelData = $panelModel->getPanel($side);

      if(!empty($panelData)){
         $panelTemplate = new Template();

         foreach ($panelData as $panel) {
            // Nastavení prováděné kategorie
            self::$currentCategory = $category
            = array(Category::COLUMN_CAT_LABEL => $panel->{Category::COLUMN_CAT_LABEL},
               Category::COLUMN_CAT_ID => $panel->{Category::COLUMN_CAT_ID});

            // Vytvoření tabulek
            $tableIndex = 1; $moduleDbTables = array();
            $objectName=self::MODULE_DBTABLES_PREFIX.$tableIndex;
            while (isset($panel->$objectName) AND ($panel->$objectName != null)){
               $moduleDbTables[$tableIndex] = $panel->$objectName;
               $tableIndex++;
               $objectName=self::MODULE_DBTABLES_PREFIX.$tableIndex;
            }

            // Příprava modulu
            $panelModule = new Module($panel, $moduleDbTables);
            self::$selectedModule = clone $panelModule;
            $panelClassName = ucfirst($panelModule->getName()).self::MODULE_PANEL_CLASS_SUFIX;

            // Spuštění panelu
            try {
               if(!file_exists('.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $panelModule->getName() . DIRECTORY_SEPARATOR . 'panel.class.php')){
                  throw new BadFileException(_("Controler a Viewer panelu ").$panelModule->getName()._(" neexistuje."),15);
               }
               include '.' . DIRECTORY_SEPARATOR . self::MODULES_DIR . DIRECTORY_SEPARATOR . $panelModule->getName() . DIRECTORY_SEPARATOR . 'panel.class.php';

               if(!class_exists($panelClassName)){
                  throw new BadClassException(_("Třídat ").$panelClassName._(" panelu ").$panelModule->getName()._(" neexistuje."),16);
               }

               //	vytvoření pole se skupinama a právama
               $userRights = array();
               foreach ($panel as $collum => $value){
                  if (substr($collum,0,strlen(Rights::RIGHTS_GROUPS_TABLE_PREFIX)) == Rights::RIGHTS_GROUPS_TABLE_PREFIX){
                     $userRights[substr($collum,strlen(Rights::RIGHTS_GROUPS_TABLE_PREFIX), strlen($collum))]=$value;
                  }
               }

               //	Vytvoření objektu pro přístup k právům modulu
               $panelRights = new Rights($userRights);

               //	nastavení tempalte
               $panelTemplate->setModule($panelModule);
               $link = new Links(true);
               $panelTemplate->setTplCatLink($link->category($panel->{CategoryModel::COLUMN_CAT_LABEL},
                     $panel->{CategoryModel::COLUMN_CAT_ID}));
               unset ($link);

               //	CONTROLLER PANELU
               $panel = new $panelClassName($category, $panelTemplate, $panelRights);

               //	spuštění controleru
               if(!method_exists($panel, self::MODULE_PANEL_CONTROLLER)){
                  throw new BadMethodCallException(_("Neexistuje controler panelu ").$panelModule->getName(),17);
               }
               Locale::switchToModuleTexts(); //jazykové nastavení na modul
               $panel->{self::MODULE_PANEL_CONTROLLER}();
               Locale::switchToEngineTexts(); //jazykové nastavení na engine

               if(!method_exists($panel, self::MODULE_PANEL_VIEWER)){
                  throw new BadMethodCallException(_("Neexistuje viewer panelu ").$panelModule->getName(),18);
               }
               Locale::switchToModuleTexts(); //jazykové nastavení na modul
               $panel->{self::MODULE_PANEL_VIEWER}();
               Locale::switchToEngineTexts(); //jazykové nastavení na engine

               AppCore::setSelectedModule();

            } catch (BadClassException $e) {
               new CoreErrors($e);
            } catch (BadFileException $e) {
               new CoreErrors($e);
            } catch (BadMethodCallException $e) {
               new CoreErrors($e);
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
      Category::factory(AppCore::getAuth());
      $this->assignVarToTpl("MAIN_CATEGORY_TITLE", Category::getLabel());
      $this->assignVarToTpl("MAIN_CATEGORY_ID", Category::getId());
   }

   public function assignCoreErrorsToTpl() {
      //      $this->assignVarToTpl("CORE_ERRORS", CoreException::getAllExceptions());
      //      $this->assignVarToTpl("CORE_ERRORS_EMPTY", CoreException::isEmpty());
      $this->assignVarToTpl("ERROR_NAME", _('Chyba'));
      $this->assignVarToTpl("ERROR_IN_FILE", _('soubor'));
      $this->assignVarToTpl("ERROR_IN_FILE_LINE", _('řádek'));
      $this->assignVarToTpl("ERROR_TRACE", _('řádek'));
      $this->assignVarToTpl("CORE_ERRORS", CoreErrors::getErrors());
      $this->assignVarToTpl("CORE_ERRORS_EMPTY", CoreErrors::isEmpty());
   }

    /**
     * Metoda přiřadí všechny proměnné do šablonovacího systému
     * //TODO není omplementována, vytvořit načítání do šablony.
     */
   public function assignMessagesToTpl() {
      $this->assignVarToTpl("MESSAGES", self::getInfoMessages()->getMessages());
      $this->assignVarToTpl("ERRORS", self::getUserErrors()->getMessages());
   }

    /**
     * Metoda vytváří sitemapu a odesílá ji na výstup
     *
     */
   public function runSitemap() {
      $sitemapItems = new SitemapModel();
      $sitemapItems = $sitemapItems->getItems();

      // vyttvoření hlavní kategorie
      $sitemap = new SiteMap(new Links(true, true), AppCore::sysConfig()->getOptionValue('sitemap_periode'), '1.0');
      $sitemap->run();
      unset ($sitemap);
      // procházení kategoríí na vytváření sitemapy
      if($sitemapItems != null){
         foreach ($sitemapItems as $itemIndex => $item) {
            //				Vytvoření objektu pro práci s modulem
            $module = new Module($item, $this->getModuleTables($item));
            AppCore::setSelectedModule($module);

            $link = new Links(true);
            $link = $link->category($item->{Category::COLUMN_CAT_LABEL_ORIG}, $item->{Category::COLUMN_CAT_ID});

            $moduleName = ucfirst($module->getName());
            $moduleClass = $moduleName.self::MODULE_SITEMAP_SUFIX_CLASS;
            //				Pokud existuje soubor tak jej načteme
            if(file_exists($module->getDir()->getMainDir(false).strtolower(self::MODULE_SITEMAP_SUFIX_CLASS).'.class.php')){
               include ($module->getDir()->getMainDir(false).strtolower(self::MODULE_SITEMAP_SUFIX_CLASS).'.class.php');
               if(class_exists($moduleClass)){
                  $sitemap = new $moduleClass($link, $item->{SitemapModel::COLUMN_SITEMAP_FREQUENCY},
                     $item->{SitemapModel::COLUMN_SITEMAP_PRIORITY});

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
         //			Vygenerování mapy
         //			Podle vyhledávače
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
      $this->coreTpl->addVar("PAGE_NOT_FOUND", true);
      $this->coreTpl->addVar("PAGE_NOT_FOUND_TEXT", _('Chyba 404 - Stránka nebyla nalezena.
Zkontrolujte prosím zadanou adresum nebo přejděte na'));
      $this->coreTpl->addVar("PAGE_NOT_FOUND_MAIN_PAGE", _(' hlavní stránku'));
   }

    /**
     * Metoda načte soubor se specialními vlastnostmi přenesenými do šablony,
     * které jsou jednotné pro celý web
     */
   private function initialWebTpl() {
      $fileName = 'initial'.ucfirst(UrlRequest::getMediaType()).'.php';
      if(file_exists(self::MAIN_ENGINE_PATH.self::MODULES_DIR.'/'.$fileName)){
         require self::MAIN_ENGINE_PATH.self::MODULES_DIR.'/'.$fileName;
      }
   }

    /**
     * Hlavní metoda provádění aplikace
     */
   public function runApp() {
      //autorizace přístupu
      self::coreAuth();

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
               $eplugin->setAuthParam($this->auth);
               $eplugin->initRunOnlyEplugin();
               return true;
            } else if(class_exists($epluginWithOutEplugin)){
               $eplugin = new $epluginWithOutEplugin();
               $eplugin->setAuthParam($this->auth);
               $eplugin->initRunOnlyEplugin();
               return true;
            } else {
               new CoreException(_('Požadovaný eplugin nebyl nalezen'), 19);
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
               new CoreException(_('Požadovaný jsplugin nebyl nalezen'), 20);
            }
            unset($jspluginName);
         }
         //		Pokud se načítá statická část sitemap
         else if(UrlRequest::getSupportedServicesType() == UrlRequest::SUPPORTSERVICES_SITEMAP_NAME){
            $this->runSitemap();
         }
      }
      // pokud je zpracovávána normální aplikace a její moduly
      else {
         switch (UrlRequest::getMediaType()) {
            case UrlRequest::MEDIA_TYPE_WWW:
               default:
                  //				Výchozí je zobrazena stránka =======================================
                  //				Příprava šablony
                  //				inicializace šablonovacího systému
                  $this->_initTemplate();

                  //nastavení vybrané kategorie
                  $this->selectCategory();

                  // Globální inicializace proměných do šablony
                  $this->initialWebTpl();

                  //vytvoření hlavního menu
                  $this->createMainMenu();

                  // Pokud není chyba spustíme moduly
                  if(!AppCore::isErrorPage()){
                     //		spuštění modulů
                     $this->runModules();
                  }

                  //		spuštění panelů
                  //		Levý
                  if(Category::isLeftPanel()){
                     $this->runPanel('left');
                  }
                  //		Pravý
                  if(Category::isRightPanel()){
                     $this->runPanel('right');
                  }

                  // pokud se v aplikaci vyskitla chybová stránka, spustíme ji
                  if(self::isErrorPage()){
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
                  break;
         }
      }
   }
}

?>