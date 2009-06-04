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

class Template {
   /**
    * Proměná s názvem titulku okna
    * @var string
    */
   private $pageTitle = null;

   /**
    * Pole s funkcemi spoštěnými při načtení stránky
    * @var array
    */
   private static $onLoadJsFunctions = array();

   // NEW ====================================================================
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
    * @var ModuleSys
    */
   protected $moduleSys = null;

   /**
    * Pole s Epluginama
    * @var array
    */
   private $eplugins = array();

   /*
    * ========== STATICKÉ METODY
    */

   public static function factory() {
      self::setFace(AppCore::sysConfig()->getOptionValue(self::FACE_CONFIG_PARAM));
   }

   /**
    * Metoda nastaví název vzhledu webu (faces)
    * @param string $face -- název vzhledu
    */
   public static function setFace($face) {
      self::$face = $face;
   }

   /*
    * ========== VEŘEJNÉ METODY
    */

   // nastaví privátní proměnou
   public function setVar($name, $value, $array = false) {
      if(!$array){
//         $this->privateVars[$name] = new TplVar($name, $value);
         $this->privateVars[$name] = $value;
      } else {
         if(!isset ($this->privateVars[$name]) OR !is_array($this->privateVars[$name])){
            $this->privateVars[$name] = array();
         }
         if($array === true){
//            array_push($this->privateVars[$name], new TplVar($name, $value));
            array_push($this->privateVars[$name], $value);
         } else {
//            $this->privateVars[$name][$array] = new TplVar($name, $value);
            $this->privateVars[$name][$array] = $value;
         }
      }
   }

   // vrací privátní proměnou
   public function v($name) {
      $return = null;
      if (isset ($this->privateVars[$name])){
         return $this->privateVars[$name];
      } else {
         return null;
      }
   }

   // nastaví globální proměnou
   public function setPublicVar($name, $value) {
      self::$publicVars[$name] = new TplVar($name, $value);
   }

   // vrací globální proměnou
   public function vPublic($name) {
      $return = null;
      if (isset (self::$publicVars[$name])){
         return self::$publicVars[$name]->get();
      } else {
         return null;
      }
   }

   public function setLink($name, Links $link = null) {
      if($link == null){
         $this->templateLinks[$name] = $this->sys()->link();
      } else {

      }
   }

   // vrácení odkazu
   public function l($name = null) {
      if($name == null AND $this->sys() != null){
         return $this->sys()->link();
      } else if(isset ($this->templateLinks[$name])) {
         return $this->templateLinks[$name];
      } else {
         return new Links();
      }
   }
// vrácení odkazu - alias
   public function link($name = null) {
      return $this->l($name);
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


   // přidá název šablony do seznamu renderovaných šablon objektu
   public function addTplFile($name, $engine = false) {
      if(!$engine AND $this->sys()->module() instanceof Module){
         array_push($this->templateFiles, self::getFileDir($name, self::TEMPLATES_DIR,
            $this->sys()->module()->getName()).$name);
      } else {
         array_push($this->templateFiles, self::getFileDir($name, self::TEMPLATES_DIR, false).$name);
      }
   }

   // vykreslí šablonu
   public function renderTemplate() {
      // zastavení výpisu buferu
      ob_start();

      foreach ($this->templateFiles as $file) {
         include_once $file;
      }

      ob_end_flush();
   }

   /**
    * Metoda vloží šablonu na zadané místo
    * @param string $name -- název šablony
    * @param boolean $engine -- (option) jestli se jedná o šablonu enginu
    */
   public function includeTpl($name, $engine = false) {
      if(!$engine AND $this->sys()->module() instanceof Module){
         include_once (self::getFileDir($name, self::TEMPLATES_DIR,
            $this->sys()->module()->getName())).$name;
      } else {
         include_once (self::getFileDir($name, self::TEMPLATES_DIR, false)).$name;
      }
   }

   /**
    * Metoda vrací název zvoleného vhledu
    * @return string -- název vzhledu
    */
   public function face() {
      return self::$face;
   }

   /**
    * Metoda vrací adresář zvoleného vhledu
    * @return string -- adresář vzhledu
    */
   public function faceDir() {
      return AppCore::MAIN_ENGINE_PATH.self::FACES_DIR.DIRECTORY_SEPARATOR.$this->face();
   }

   // přidá podřízený objekt šablony
   public function addTplObj($name, Template $obj, $array = false) {
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
   public function includeTplObj($name) {
      if($name instanceof Template){
         $name->renderTemplate();
      } else {
         if(isset ($this->templateObjects[$name])){
            $this->templateObjects[$name]->renderTemplate();
         }
      }
   }

   /**
    * Metoda přidá EPlugin do šablony
    * @param string $name -- název epluginu v šabloně
    * @param Eplugin $eplugin -- samotný Eplugin
    */
   final public function addEplugin($name, Eplugin $eplugin) {
      $this->eplugins[$name] = clone $eplugin;
   }

   /*
    * ========== Metody pro nastavení šablony
    */
   public function _setSysModule(ModuleSys $module) {
      $this->moduleSys = clone $module;
   }



   /*
    * ========== PRIVÁTNÍ METODY
    */

    /**
     * Metoda vrací název adresáře s požadovaným souborem (bez souboru)
     * @param string $file -- název souboru
     * @param string $type -- typ adresáře - konstanta třídy
     * @param boolean $engine -- jestli se jedná o objekt enginu nebo modulu
     * @return string -- adresář bez souboru
     */
   public static function getFileDir($file, $type = self::TEMPLATES_DIR, $moduleName = false) {
      $moduleDir = null;
      if($moduleName){
         $moduleDir = AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR;
      }
//      echo AppCore::MAIN_ENGINE_PATH.self::FACES_DIR.DIRECTORY_SEPARATOR
//            .self::$face.DIRECTORY_SEPARATOR.$moduleDir.$type.DIRECTORY_SEPARATOR.$file;
      // první zkusíme jestli existuje soubor v šaboně webu
      try {
         if(file_exists(AppCore::MAIN_ENGINE_PATH.self::FACES_DIR.DIRECTORY_SEPARATOR
               .self::$face.DIRECTORY_SEPARATOR.$moduleDir.$type.DIRECTORY_SEPARATOR.$file)){
            return AppCore::MAIN_ENGINE_PATH.self::FACES_DIR.DIRECTORY_SEPARATOR
            .self::$face.DIRECTORY_SEPARATOR.$moduleDir.$type.DIRECTORY_SEPARATOR;
         }
         // jesti existuje alespoň výchozí soubor v default faces
         //         else if(){
         //
         //         }
         // použijeme soubor přímo s enginu
         else if(file_exists(AppCore::MAIN_ENGINE_PATH.$moduleDir.$type.DIRECTORY_SEPARATOR.$file)) {
            return AppCore::MAIN_ENGINE_PATH.$moduleDir.$type.DIRECTORY_SEPARATOR;
         } else {
            throw new InvalidArgumentException(sprintf(_('Neexistující soubor %s'), $file));
         }
      } catch (InvalidArgumentException $e) {
         new CoreErrors($e);
      }
   }


   /**
    * Metoda vrací objekt modulu
    * @return Module
    */
   protected function module() {
      return $this->sys()->module();
   }

   /**
    * Metoda vrqací systémový objekt modulu
    * @return ModuleSys
    */
   final public function sys() {
      return $this->moduleSys;
   }

   /**
    * Metoda přeloží zadaný řetězec
    * @param string $message -- řetězec k přeložení
    * @return string -- přeložený řetězec
    */
   final public function _m($message) {
      return $this->sys()->locale()->_m($message);
   }

   /**
    * Konstruktor třídy
    */
   function __construct(){}


























   /**
    * Metoda vrací objekt modulu
    * @return Module -- objekt modulu
    */
//   private function getModule() {
//      return $this->module;
//   }

   /**
    * Metoda přidává zadanou šablonu do výstupu
    *
    * @param string/array/Eplugin -- název šablony nebo objekt Epluginu
    * @param boolean -- true pokud má být použita systémová šablona
    */
//   public function addTpl($tplName, $engineTpl = false, $tplId = 1){
//      $this->checkTemplatesArray();
//      // pokud se jedná o eplugin tak vložíme části
//      if(class_exists(get_class($tplName), false) AND get_parent_class($tplName) == 'Eplugin'){
//         $epl = $tplName;
//         $this->addTpl($epl->getTpl(), true);
//         $epl->assignToTpl($this);
//         return true;
//      }
//
//      //TODO kontrola souborů
//      //přidání šablony do pole s šablonami modulu
//      if($this->getModule() != null){
//         if($engineTpl == false){
//            if(!is_array($tplName)){
//               array_push($this->templates[$this->getModule()->getId()][self::TEMPLATES_ARRAY_NAME],
//                  array(self::TEMPLATE_FILE_NAME => $this->selectModuleTemplateFaceFile($tplName),
//                     self::TEMPLATE_ID_NAME =>  $tplId));
//            } else {
//               foreach ($tplName as $tpl) {
//                  array_push($this->templates[$this->getModule()->getId()][self::TEMPLATES_ARRAY_NAME],
//                     array(self::TEMPLATE_FILE_NAME => $this->selectModuleTemplateFaceFile($tpl),
//                        self::TEMPLATE_ID_NAME =>  $tplId));
//               }
//            }
//         } else {
//            if(!is_array($tplName)){
//               array_push($this->templates[$this->getModule()->getId()][self::TEMPLATES_ARRAY_NAME],
//                  array(self::TEMPLATE_FILE_NAME => $this->selectGlobalTemplateFaceFile($tplName),
//                     self::TEMPLATE_ID_NAME =>  $tplId));
//            } else {
//               foreach ($tplName as $tpl) {
//                  array_push($this->templates[$this->getModule()->getId()][self::TEMPLATES_ARRAY_NAME],
//                     array(self::TEMPLATE_FILE_NAME => $this->selectGlobalTemplateFaceFile($tpl),
//                        self::TEMPLATE_ID_NAME =>  $tplId));
//               }
//            }
//         }
//      }
//   }

   /**
    * Metoda zjistí jestli šablona modulu existuje pro zadaný vzhled, a podle něj ji vrátí
    */
//   private function selectModuleTemplateFaceFile($file) {
//      //		zvolení vzhledu
//      //		vybraný vzhled
//      if(file_exists(AppCore::getTepmlateFaceDir().$this->getModule()->getDir()->getTemplatesDir(false).$file)){
//         $faceFile = AppCore::getTepmlateFaceDir().$this->getModule()->getDir()->getTemplatesDir(false).$file;
//      }
//      //		Výchozí vzhled
//      else if(file_exists(AppCore::getTepmlateDefaultFaceDir().$this->getModule()->getDir()->getTemplatesDir(false).$file)){
//         $faceFile = AppCore::getTepmlateDefaultFaceDir().$this->getModule()->getDir()->getTemplatesDir(false).$file;
//      }
//      //		Vzhled v engine
//      else {
//         $faceFile = $this->getModule()->getDir()->getTemplatesDir().$file;
//      };
//      return $faceFile;
//   }

   /**
    * Metoda zjistí jestli globální šablona existuje pro zadaný vzhled, a podle něj ji vrátí
    */
//   private function selectGlobalTemplateFaceFile($file) {
//      //		zvolení vzhledu
//      //		vybraný vzhled
//      if(file_exists(AppCore::getTepmlateFaceDir().AppCore::TEMPLATES_DIR.DIRECTORY_SEPARATOR.$file)){
//         $faceFile = AppCore::getTepmlateFaceDir().AppCore::TEMPLATES_DIR.DIRECTORY_SEPARATOR.$file;
//      }
//      //		Výchozí vzhled
//      else if(file_exists(AppCore::getTepmlateDefaultFaceDir().AppCore::TEMPLATES_DIR.DIRECTORY_SEPARATOR.$file)){
//         $faceFile = AppCore::getTepmlateDefaultFaceDir().AppCore::TEMPLATES_DIR.DIRECTORY_SEPARATOR.$file;
//      }
//      //		Vzhled v engine
//      else {
//         $faceFile = $file;
//      };
//      return $faceFile;
//   }

   /**
    * Metody kontroluje vytvoření pole s moduly (items) kategorie
    */
//   private function checkTemplatesArray(){
//      if($this->getModule() != null){
//         if(!isset($this->templates[$this->getModule()->getId()])){
//            $this->templates[$this->getModule()->getId()] = array();
//            $this->templates[$this->getModule()->getId()][self::TEMPLATES_ARRAY_NAME] = array();
//         }
//      }
//   }

   /**
    * Metoda zjistí jestli stylesheet modulu existuje pro zadaný vzhled, a podle něj jej vrátí
    */
//   private function selectModuleStylesheetFaceFile($file) {
//      //		zvolení vzhledu
//      //		vybraný vzhled
//      if(file_exists(AppCore::getTepmlateFaceDir().$this->getModule()->getDir()->getStylesheetsDir(false).$file)){
//         $faceFile = AppCore::getTepmlateFaceDir(false).$this->getModule()->getDir()->getStylesheetsDir(false).$file;
//      }
//      //		Výchozí vzhled
//      else if(file_exists(AppCore::getTepmlateDefaultFaceDir().$this->getModule()->getDir()->getStylesheetsDir(false).$file)){
//         $faceFile = AppCore::getTepmlateDefaultFaceDir(false).$this->getModule()->getDir()->getStylesheetsDir(false).$file;
//      }
//      //		Vzhled v engine
//      else {
//         $faceFile = $this->getModule()->getDir()->getStylesheetsDir().$file;
//      };
//      return $faceFile;
//   }

   /**
    * statická metoda vrací pole se styly
    * @return array -- pole se styly (obsahuje i cestu)
    */
   //   public static function getStylesheets() {
   //      return self::$stylesheets;
   //   }

   /**
    * statická metoda vrací pole s javascripty
    * @return array -- pole s javascripty (obsahuje i cestu)
    */
   //   public static function getJavaScripts() {
   //      return self::$javascripts;
   //   }

   /**
    * Metoda nastaví název modulu, který bude vypsán na začátku
    * @param string -- název
    * @param boolena -- (option) jesli se má název přidat za stávající nebo přepsat
    */
//   public function setTplLabel($name, $merge = false) {
//      if($this->getModule() != null){
//         if($merge){
//            $this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_LABEL].=$name;
//         } else {
//            $this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_LABEL]=$name;
//         }
//      }
//   }

   /**
    * Metoda nastaví podnázev modulu, který bude vypsán na začátku
    * @param string -- podnázev
    * @param boolena -- (option) jesli se má název přidat za stávající nebo přepsat
    * @param string -- (option) oddělovač mezi více nadpisy (default '-')
    */
//   public function setTplSubLabel($name, $merge = false, $separator = '-') {
//      if($this->getModule() != null){
//         if($merge){
//            if(isset($this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_SUBLABEL]) AND $this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_SUBLABEL] != null){
//               $separator = ' '.$separator.' ';
//            } else {
//               $separator = null;
//               $this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_SUBLABEL] = null;
//            }
//            $this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_SUBLABEL].=$separator.$name;
//         } else {
//            $this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_SUBLABEL]=$name;
//         }
//      }
//   }

   /**
    * Metoda nastaví podtitulek modulu, který bude vypsán ve jménu okna
    * @param string -- podnázev
    * @param boolena -- (option) jesli se má název přidat za stávající nebo přepsat
    * @param string -- (option) oddělovač mezi více nadpisy (default '-')
    */
//   public function setSubTitle($name, $merge = false, $separator = '-') {
//      if($merge){
//         if($this->pageTitle != null){
//            $separator = ' '.$separator.' ';
//         } else {
//            $separator = null;
//         }
//         $this->pageTitle.=$separator.$name;
//      } else {
//         $this->pageTitle=$name;
//      }
//   }

   /**
    * Metoda vrací přiřazený název titulku okna
    * @return string -- titulek okna
    */
//   public function getSubTitle() {
//      return $this->pageTitle;
//   }

   /**
    * Metoda nastaví popis (alt) modulu, který bude vypsán na začátku
    * @param string -- popis
    * @param boolena -- (option) jesli se má popis přidat za stávající nebo přepsat
    */
//   public function setTplAlt($name, $merge = false) {
//      if($this->getModule() != null){
//         if($merge){
//            $this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_ALT].=$name;
//         } else {
//            $this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_ALT]=$name;
//         }
//      }
//   }

   /**
    * Metoda nastaví popis link na kategorii s modulem, který bude vypsán na začátku
    * (použití asi jenom v panelech)
    * @param string -- link
    */
//   public function setTplCatLink($link = null) {
//      if($link == null){
//         $link = new Links();
//      }
//      if($this->getModule() != null){
//         $this->templates[$this->getModule()->getId()][self::TEMPLATE_CATEGORY_LINK]=$link;
//      }
//   }

   /**
    * Metoda přiřazuje proměnné do šablony
    * @param string -- název proměnné
    * @param string/array -- hodnota proměnné
    * @param boolean -- true pokud má být proměná zařazena do modulu (default: true)
    */
//   public function addVar($varName, $varValue, $isModuleVar = true) {
//      $this->checkVarsArray();
//      if($isModuleVar AND $this->getModule() != null){
//         $this->templates[$this->getModule()->getId()][self::VARIABLES_ARRAY_NAME][$varName] = $varValue;
//      } else if($isModuleVar){
//         $this->templates[$varName] = $varValue;
//      } else {
//         $this->engineVars[$varName] = $varValue;
//      }
//   }

   /**
    * Metody zkontroluje vytvoření pole s proměnnými v šabloně
    */
//   private function checkVarsArray() {
//      //		kontrola hlavního pole
//      $this->checkTemplatesArray();
//      if($this->getModule() != null){
//         if(!isset($this->templates[$this->getModule()->getId()][self::VARIABLES_ARRAY_NAME])){
//            $this->templates[$this->getModule()->getId()][self::VARIABLES_ARRAY_NAME] = array();
//         }
//      }
//   }

   /**
    * Metoda vrací pole s šablonami a proměnými
    * @return array -- pole šablon a proměných
    */
//   public function getTemplatesArray() {
//      return $this->templates;
//   }

   /**
    * Metoda vrací pole s proměnými předanými do enginu
    * @return array -- pole proměných
    */
//   public function getEngineVarsArray() {
//      return $this->engineVars;
//   }

   /**
    * Metoda nastavuje modul
    * @param Module -- objekt modulu
    */
//   public function setModule(Module $module=null) {
//      $this->module = $module;
//      $this->checkTemplatesArray();
//      $this->setTplLabel($this->getModule()->getLabel());
//      $this->setTplAlt($this->getModule()->getAlt());
//      //		Přiřazení identifikátoruModulu
//      $this->templates[$this->getModule()->getId()][self::TEMPLATE_MODULE_STYLE_IDENT] = $this->getModule()->getName();
//   }

   /**
    * Metoda přidává zadaný JsPlugin do šablony
    *
    * @param JsPlugin -- objekt js pluginu
    */
//   final public function addJsPlugin(JsPlugin $jsPlugin){
//      if(get_parent_class($jsPlugin) != 'JsPlugin'){
//         throw new InvalidArgumentException(sprintf(_('Parametr "%s" naní objekt JsPluginu'), $jsPlugin),1);
//      }
//      $files = $jsPlugin->getAllJsFiles();
//      foreach ($files as $file) {
//         $this->addJS($file, true);
//      }
//      $files = $jsPlugin->getAllCssFiles();
//      foreach ($files as $file) {
//         $this->addCss($file, true);
//      }
//   }

   /**
    * Metoda přidá funkci do parametru OnLoad při načtení stránky
    * @param string -- název funkce pro nahrání
    */
//   final public function addJsOnLoad($jsFunction) {
//      array_push(self::$onLoadJsFunctions, $jsFunction);
//   }

   /**
    * Metoda vrací pole s js funkcemi určenými k načtení po nahrátí stránky
    * @return array -- pole s funkcemi
    */
//   
}
?>