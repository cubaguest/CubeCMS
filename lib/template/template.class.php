<?php
/**
 * Třída pro práci s šablonami modulu.
 * Třida obsahuje vrstvu mezi šablonovacím systémem a samotným pohledem (viewrem).
 * Umožňuje všechny základní operace při volbě a plnění šablony a jejímu zobrazení.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro obsluhu šablony
 */

require_once 'tplmodifiers.php';

class Template {
   /**
    * Názvem adresáře se vzhledy
    */
   const FACES_DIR = 'faces';

   /**
    * Názvem položky s nastavením face
    */
   const FACE_CONFIG_PARAM = 'face';

   /**
    * Konstanta obsahuje výchozí název vzhledu (face)
    */
   const FACE_DEFAULT_NAME = 'default';

   /**
    * Název adresáře s obrázky šablony
     */
   const IMAGES_DIR = "images";

   /**
    * Názvem adresáře se styly
    */
   const STYLESHEETS_DIR = 'stylesheets';

   /**
    * Název adresáře s šablonami
    */
   const TEMPLATES_DIR = 'templates';

   /**
    * Název adresáře s javascripty
    */
   const JAVASCRIPTS_DIR = 'jscripts';

   /**
    * Název adresáře s js pluginy
    */
   const JSPLUGINS_DIR = 'templates';

   /**
    * Název faces (vzhledu)
    * @var string
    */
   private static $face = 'default';

   /**
    * Pole s veřejnýma proměnnýma
    * @var array
    */
   private static $publicVars = array();

   /**
    * Pole s privátními proměnnými
    * @var array
    */
   protected $privateVars = array();

   /**
    * Pole s šablonama
    * @var array
    */
   protected $templateFiles = array();

   /**
    * Pole s podřízenými objekty šablony
    * @var array
    */
   protected $templateObjects = array();

   /**
    * pole s odkazy
    * @var array
    */
   protected $templateLinks = array();

   /**
    * Statické pole s css styly
    * @var array
    */
   protected static $stylesheets = array();

   /**
    * Statické pole s javascript soubory
    * @var array
    */
   protected static $javascripts = array();

   /**
    * Objekt vlastností modulu (jeho klon)
    * @var Module_Sys
    */
   protected $moduleSys = null;

   /**
    * Proměná s názvem titulku okna kategorie
    * @var string
    */
   private static $catTitle = false;

   /**
    * Proměná s názvem titulku okna článku
    * @var string
    */
   private static $articleTitle = false;

   /**
    * Proměná s názvem titulku okna akce
    * @var string
    */
   private static $actionTitle = false;


   /*
    * ============= MAGICKÉ METODY
    */

   /**
    * Konstruktor třídy
    * @param Module_Sys $modulesys -- systémový objekt modulu a práv
    */
   public function __construct(Module_Sys $moduleSys = null){
      if($moduleSys != null){
         $this->_setSysModule($moduleSys);
      }
   }

   /**
    * Magická metoda pro vložení neinicializované proměné do objektu
    * @param string $name -- název proměnné
    * @param mixed $value -- hodnota proměnné
    */
   public function  __set($name, $value) {
      $this->privateVars[$name] = $value;
   }

   /**
    * Metoda vraci inicializovanou proměnnou, pokud je
    * @param string $name -- název proměnné
    * @return mixed -- hodnota proměnné
    */
   public function  __get($name) {
      if(isset($this->privateVars[$name])){
         return $this->privateVars[$name];
      } else {
         return null;
      }
   }

   /**
    * Metoda kontroluje jestli byla daná proměnná inicializována
    * @param string $name -- název proměnné
    * @return mixed -- hodnota proměnné
    */
   public function  __isset($name) {
      return isset($this->privateVars[$name]);
   }

   /**
    * Metoda maže danou proměnnou z objektu
    * @param string $name -- název proměnné
    */
   public function  __unset($name) {
      if(isset ($this->privateVars[$name])){
         unset ($this->privateVars[$name]);
      }
   }

   /*
    * ========== VEŘEJNÉ METODY
    */

   /**
    * Metoda nasatvi privátní proměnnou šablony
    * @param string $name -- název proměnné
    * @param mixed $value -- hodnota proměnné
    */
   public function setVar($name, $value) {
      $this->privateVars[$name] = $value;
   }

   /**
    * Metoda nastaví globální proměnnou pro celou šablonu
    * @param string $name -- název proměnné
    * @param mixed $value -- hodnota proměnné
    */
   public function setPVar($name, $value) {
      self::$publicVars[$name] = $value;
   }

   // vrací globální proměnou
   /**
    * Metoda vrací hodnotu veřejné proměnné
    * @param string $name -- název proměnné
    * @return mixed -- hodnota proměnné
    */
   public function pVar($name) {
      $return = null;
      if (isset (self::$publicVars[$name])){
         return self::$publicVars[$name];
      } else {
         return null;
      }
   }

   /**
    * Metoda nastaví název titulku pro kategorii
    * @param string $text -- název kategorie
    */
   final public function setCatTitle($text) {
      self::$catTitle = $text;
   }

   /**
    * Metoda vrátí název titulku kategorie
    * @return string
    */
   final public function catTitle() {
      return self::$catTitle;
   }

   /**
    * Metoda nastaví název titulku pro článek
    * @param string $text -- název článek
    */
   final public function setArticleTitle($text) {
      self::$articleTitle = $text;
   }

   /**
    * Metoda vrátí název titulku článeku
    * @return string
    */
   final public function articleTitle() {
      return self::$articleTitle;
   }

   /**
    * Metoda nastaví název titulku pro akci
    * @param string $text -- název akce
    */
   final public function setActionTitle($text) {
      self::$actionTitle = $text;
   }

   /**
    * Metoda vrátí název titulku akce
    * @return string
    */
   final public function actionTitle() {
      return self::$actionTitle;
   }

   // nepoužito zatím
   public function setLink($name, Links $link = null) {
      if($link == null){
         $this->templateLinks[$name] = $this->sys()->link();
      } else {
         $this->templateLinks[$name] = $link;
      }
   }

   /**
    * Metoda vrátí objekt odkazu na aktuální kategorii a modul
    * @param boolean $clear -- jestli má být odkaz prázdný
    * @return Links
    */
   public function l($clear = false) {
      //      if($name == null AND $this->sys() != null){
      if($this->sys() != null){
         $link = $this->sys()->link();
         //      }
         //      else if(isset ($this->templateLinks[$name])) {
         //         $link = $this->templateLinks[$name];
      } else {
         $link = new Links();
      }
      if($clear){
         $link->clear();
      }
      return $link;
   }

   /**
    * Metoda vrátí objekt odkazu na aktuální kategorii a modul (alias na l())
    * @param boolean $clear -- jestli má být odkaz prázdný
    * @return Links
    */
   public function link($clear = false) {
      return $this->l($clear);
   }

   /**
    * Metoda vrací obsah prvku post, ošetřený o specielní znaky
    * @param string $name -- název prvku
    * @param string $defaultValue -- výchozí hodnota pokud prvek nebyl odeslán
    * @return string -- hodnota ošetřená o specielní znaky nebo výchozí hodnota
    */
   public function post($name, $defaultValue = null) {
      if(isset ($_POST[$name])){
         return htmlspecialchars($_POST[$name]);
      } else {
         return $defaultValue;
      }
   }

   /**
    * Metoda přidá požadovaný soubor šablony do výstupu
    * @param string $name -- název souboru
    * @param boolean $engine -- jestli se jedná o šablonu enginu
    */
   public function addTplFile($name, $engine = false) {
      if(!$engine AND $this->sys() != null AND $this->sys()->module() instanceof Module){
         array_push($this->templateFiles, self::getFileDir($name, self::TEMPLATES_DIR,
               $this->sys()->module()->getName()).$name);
      } else {
         array_push($this->templateFiles, self::getFileDir($name, self::TEMPLATES_DIR, false).$name);
      }
   }

   /**
    * Metoda vykreslí danou šablonu a její výsledek odešle na výstup
    */
   public function renderTemplate() {
      // zastavení výpisu buferu
      ob_start();
      foreach ($this->templateFiles as $file) {
         if(file_exists($file)){
            include $file;
         }
      }
      ob_end_flush();
   }

   /**
    * Metoda vloží šablonu na zadané místo
    * @param string $name -- název šablony
    * @param boolean $engine -- (option) jestli se jedná o šablonu enginu
    */
   public function includeTpl($name, $engine = false, $vars = null) {
      if(!$engine AND $this->sys() != null AND $this->sys()->module() instanceof Module){
         include (self::getFileDir($name, self::TEMPLATES_DIR,
               $this->sys()->module()->getName()).$name);
      } else {
         include (self::getFileDir($name, self::TEMPLATES_DIR, false).$name);
      }
      unset ($vars);
   }

   /**
    * Metoda vrací adresář zvoleného vhledu
    * @return string -- adresář vzhledu
    */
   final public function faceDir() {
      return AppCore::MAIN_ENGINE_PATH.self::FACES_DIR.DIRECTORY_SEPARATOR.self::face();
   }

   // přidá podřízený objekt šablony
   // pravděpodobně nepotřebná
   final public function addTplObj($name, Template $obj, $array = false) {
      if($array){
         if(!isset ($this->templateObjects[$name])){
            $this->templateObjects[$name] = array();
         }
         if($array === true){
            array_push($this->templateObjects, clone $obj);
         } else {
            $this->templateObjects[$name][$array] = $obj;
         }
      } else {
         $this->templateObjects[$name] = clone $obj;
      }
   }

   // metoda vyrenderuje název objektu šablony nebo objekt šablony
   /**
    * Metoda vloží do šablony zadaný objekt šablony a vyrenderuje jej zvlášť,
    * nezávisle na objektu nadřízeném
    * @param Template $name -- objekt šablony
    */
   final public function includeTplObj($name) {
      if($name instanceof Template){
         $name->renderTemplate();
      } else if($name instanceof Eplugin){
         $name->renderEplugin();
      } else {
         if(isset ($this->templateObjects[$name])){
            $this->templateObjects[$name]->renderTemplate();
         }
      }
   }

   /**
    * Metoda vrqací systémový objekt modulu
    * @return Module_Sys
    */
   final public function sys() {
      return $this->moduleSys;
   }

   /**
    * Metoda přeloží zadaný řetězec
    * @param string $message -- řetězec k přeložení
    * @return string -- přeložený řetězec
    */
   final public function _($message) {
      return $this->sys()->locale()->_m($message);
   }

   /**
    * Metoda přeloží zadaný řetězec alias k metodě _()
    * @param string $message -- řetězec k přeložení
    * @return string -- přeložený řetězec
    * @deprecated -- lepší je použití $this->_() pro podobnost s funkcí gettextu
    */
   final public function _m($message) {
      return $this->_($message);
   }

   /**
    * Metoda přidá do šablony objekt JsPluginu
    * @param JsPlugin $jsplugin -- objekt JsPluginu
    * @return Template -- objekt sebe
    */
   final public function addJsPlugin(JsPlugin $jsplugin) {
      $jsfiles = $jsplugin->getAllJsFiles();
      foreach ($jsfiles as $file) {
         Template_Core::addJS($file);
      }
      $cssfiles = $jsplugin->getAllCssFiles();
      foreach ($cssfiles as $file) {
         Template_Core::addCss($file);
      }
      return $this;
   }

   /**
    * Metoda přidá javascript soubor do šablony
    * @param string/JsFile $jsfile -- název souboru nebo objek JsFile(pro virtuální)
    * @return Template -- objekt sebe
    */
   final public function addJsFile($jsfile, $engine = false) {
      if(!$engine AND $this->sys() != null){
         $dir = Template::getFileDir($jsfile, Template::JAVASCRIPTS_DIR, $this->sys()->module()->getName());
      } else {
         $dir = Template::getFileDir($jsfile, Template::JAVASCRIPTS_DIR);
      }
      Template_Core::addJS($dir.$jsfile);
      return $this;
   }

   /**
    * Metoda přidá zadaný css soubor do stylů stránky
    * @param string $cssfile -- css soubor
    * @return Template -- objekt sebe
    */
   final public function addCssFile($cssfile, $engine = false) {
      if(!$engine AND $this->sys() != null){
         $cssDir = Template::getFileDir($cssfile, Template::STYLESHEETS_DIR, $this->sys()->module()->getName());
      } else {
         $cssDir = Template::getFileDir($cssfile, Template::STYLESHEETS_DIR);
      }
      Template_Core::addCss($cssDir.$cssfile);
      return $this;
   }

   /*
    * ========== Metody pro nastavení šablony
    */
   /**
    * Metoda nastaví objekt modulu šablony
    * @param Module_Sys $module -- objekt modulu šablony
    */
   public function _setSysModule(Module_Sys $module) {
      $this->moduleSys = clone $module;
   }

   /*
    * ========== STATICKÉ METODY
    */

    /**
     * Metoda vrací název adresáře s požadovaným souborem (bez souboru)
     * @param string $file -- název souboru
     * @param string $type -- typ adresáře - konstanta třídy
     * @param boolean $engine -- jestli se jedná o objekt enginu nebo modulu
     * @return string -- adresář bez souboru
     */
   public static function getFileDir($file, $typeDir = self::TEMPLATES_DIR, $moduleName = false) {
      $moduleDir = null;
      if($moduleName){
         $moduleDir = AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR;
      }

      try {
         $faceDir = AppCore::MAIN_ENGINE_PATH.self::FACES_DIR.DIRECTORY_SEPARATOR
         .self::$face.DIRECTORY_SEPARATOR.$moduleDir.$typeDir.DIRECTORY_SEPARATOR;
         $engineDir = AppCore::MAIN_ENGINE_PATH.$moduleDir.$typeDir.DIRECTORY_SEPARATOR;
         if(file_exists($faceDir.$file)){
            return $faceDir;
         }
         // jesti existuje alespoň výchozí soubor v default faces
         //         else if(){
         //         }
         // použijeme soubor přímo s enginu
         else if(file_exists($engineDir.$file)) {
            return $engineDir;
         } else {
            throw new InvalidArgumentException(sprintf(
                  _('Neexistující soubor %s v adresáři enginu (%s) ani vzhledu (%s)'),
                  $file, $engineDir, $faceDir));
         }
      } catch (InvalidArgumentException $e) {
         new CoreErrors($e);
      }
   }

   /**
    * Metoda pro základní nasatvení šablonovacího systému
    */
   public static function factory() {
      self::setFace(AppCore::sysConfig()->getOptionValue(self::FACE_CONFIG_PARAM));
   }

   /**
    * Metoda nastaví název vzhledu webu (faces)
    * @param string $face -- název vzhledu
    */
   final public static function setFace($face) {
      self::$face = $face;
   }

   /**
    * Metoda vrací adresář zvoleného vhledu
    * @return string -- adresář vzhledu
    */
   final public static function face() {
      return self::$face;
   }

   /*
    * ============= CHRÁNĚNNÉ METODY
    */

   /**
    * Metoda vrací objekt modulu
    * @return Module
    */
   protected function module() {
      return $this->sys()->module();
   }

   /**
    * Metoda vrací objekt akcí modulu
    * @return Action
    */
   protected function action() {
      return $this->sys()->action();
   }
}
?>