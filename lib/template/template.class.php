<?php
/**
 * Třída pro práci s šablonami
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
require_once 'template_items.php';
require_once 'template_functions.php';

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
   protected static $face = 'default';

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
   //   protected $moduleSys = null;

   /**
    * Proměná s názvem titulku okna kategorie
    * @var string
    */
   private static $categoryName = false;

   /**
    * Proměná s názvem titulku okna článku
    * @var string
    */
   private static $pageTitle = false;

   /**
    * Objekt s odkazem pro danou šablonu
    * @var Url_Link
    */
   protected $link = null;

   /*
    * ============= MAGICKÉ METODY
    */

   /**
    * Konstruktor třídy
    * @param Module_Sys $modulesys -- systémový objekt modulu a práv
    */
   public function __construct(Url_Link $link) {
      $this->link = $link;
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
   public function  &__get($name) {
      if(!isset($this->privateVars[$name])) {
         $this->privateVars[$name] = null;
      //trigger_error(sprintf(_('Nedefinovaná proměnná %s'),$name), E_USER_NOTICE);
      }
      return $this->privateVars[$name];
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
      if(isset ($this->privateVars[$name])) {
         unset ($this->privateVars[$name]);
      }
   }

   /*
    * ========== VEŘEJNÉ METODY
    */

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
      if (isset (self::$publicVars[$name])) {
         return self::$publicVars[$name];
      } else {
         return null;
      }
   }

   /**
    * Metoda nastaví název titulku pro kategorii
    * @param string $text -- název kategorie
    */
   final public function setCategoryName($text) {
      self::$categoryName = $text;
   }

   /**
    * Metoda vrátí název titulku kategorie
    * @param bool $decode -- jestli se mají html znaky převést na entity
    * @return string
    */
   final public function categoryName($decode = false) {
      if(!$decode) {
         return htmlspecialchars_decode(self::$categoryName);
      } else {
         return self::$categoryName;
      }
   }

   /**
    * Metoda nastaví název titulku pro článek
    * @param string $text -- název článek
    */
   final public function setPageTitle($text) {
      self::$pageTitle = $text;
   }

   /**
    * Metoda vrátí název titulku článeku
    * @param bool $decode -- jestli se mají html znaky převést na entity
    * @return string
    */
   final public function pageTitle($decode = false) {
      if(!$decode) {
         return htmlspecialchars_decode(self::$pageTitle);
      } else {
         return self::$pageTitle;
      }
   }

   /**
    * Metoda vrátí objekt odkazu na aktuální kategorii a modul
    * @param boolean $clear -- jestli má být odkaz prázdný
    * @return Url_Link
    */
   public function l($clear = false) {
      $link = clone $this->link;
      if($clear) {
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
    * @param string $name -- název prvku např. "['text']['dalsi']"
    * @param string $defaultValue -- výchozí hodnota pokud prvek nebyl odeslán
    * @return string -- hodnota ošetřená o specielní znaky nebo výchozí hodnota
    */
   public function post($name, $defaultValue = null) {
      if(isset ($_POST{$name})) {
         return htmlspecialchars($_POST{$name});
      } else {
         return $defaultValue;
      }
   }

   /**
    * Metoda vrací obsah prvku $_GET, ošetřený o specielní znaky v url
    * @param string $name -- název prvku např. "['text']['dalsi']"
    * @param string $defaultValue -- výchozí hodnota pokud prvek nebyl odeslán
    * @return string -- hodnota ošetřená o specielní znaky nebo výchozí hodnota
    */
   public function get($name, $defaultValue = null) {
      if(isset ($_GET{$name})) {
         return urldecode($_GET{$name});
      } else {
         return $defaultValue;
      }
   }

   /**
    * Metoda přidá požadovaný soubor šablony do výstupu
    * @param string $name -- název souboru
    */
   public function addTplFile($name) {
   //      if(!$engine AND $this->sys() != null AND $this->sys()->module() instanceof Module){
   //         array_push($this->templateFiles, self::getFileDir($name, self::TEMPLATES_DIR,
   //               $this->sys()->module()->getName()).$name);
   //      } else {
      array_push($this->templateFiles, self::getFileDir($name, self::TEMPLATES_DIR).$name);
   //      }
   }

   /**
    * Metoda vykreslí danou šablonu a její výsledek odešle na výstup
    */
   public function renderTemplate() {
      print ($this);
   }


   /**
    * Magická metoda převede šablonu na řetězec
    * @return string -- vygenerovaný řetězec z šablon
    */
   public function  __toString() {
   // zastavení výpisu buferu
      ob_start();
      foreach ($this->templateFiles as $file) {
         if(file_exists($file)){
            include $file;
         }
      }
      $contents = ob_get_contents();
      ob_end_clean();
      return (string)$contents;
   }

   /**
    * Metoda vloží šablonu na zadané místo
    * @param string $name -- název šablony
    * @param boolean $engine -- (option) jestli se jedná o šablonu enginu
    */
   public function includeTpl($name, $engine = false, $vars = null) {
      $path = self::getFileDir($name, self::TEMPLATES_DIR);
      include $path.$name;
      unset ($vars);
   }

   /**
    * Metoda vrací adresář zvoleného vhledu
    * @return string -- adresář vzhledu
    */
   final public function faceDir() {
      return AppCore::MAIN_ENGINE_PATH.self::FACES_DIR.DIRECTORY_SEPARATOR.self::face();
   }

   /**
    * Metoda vloží do šablony zadaný objekt šablony a vyrenderuje jej zvlášť,
    * nezávisle na objektu nadřízeném
    * @param Template $name -- objekt šablony
    */
   final public function includeTplObj($name) {
      if($name instanceof Template) {
         $name->renderTemplate();
      }
      else if($name instanceof Eplugin) {
            $name->template()->renderTemplate();
         }
   }

   /**
    * Metoda přidá do šablony objekt JsPluginu
    * @param JsPlugin $jsplugin -- objekt JsPluginu
    * @return Template -- objekt sebe
    */
   final public function addJsPlugin(JsPlugin $jsplugin) {
      $jsfiles = $jsplugin->getAllFiles();
      foreach ($jsfiles as $file) {
         if($file instanceof JsPlugin_JsFile) {
            Template::addJS($file->getName());
         } else if($file instanceof JsPlugin_CssFile) {
            // pokud existuje css soubor u faces, vložíme ten
               if(file_exists(AppCore::getAppWebDir().self::FACES_DIR.DIRECTORY_SEPARATOR.Template::face(true)
                   .DIRECTORY_SEPARATOR.self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR.$file->getName(false))) {
                  Template::addCss('./'.self::FACES_DIR.DIRECTORY_SEPARATOR.Template::face(true).DIRECTORY_SEPARATOR.self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR.$file->getName(false));
               } else {
                  Template::addCss($file->getName());
               }
            }
      }
      return $this;
   }

   /**
    * Metoda přidá javascript soubor do šablony
    * @param string/JsPlugin_JsFile $jsfile -- název souboru nebo objek JsPlugin_JsFile(pro virtuální)
    * @return Template -- objekt sebe
    */
   public function addJsFile($jsfile) {
   //konttrola jestli se nejedná o URL adresu (vzdálený soubor)
      if(eregi('http://[a-zA-Z_.]+', $jsfile)) {
         Template::addJS($jsfile);
      } else {
         Template::addJS(Template::getFileDir($jsfile, Template::JAVASCRIPTS_DIR).$jsfile);
      }
      return $this;
   }

   /**
    * Metoda přidá zadaný css soubor do stylů stránky
    * @param string $cssfile -- css soubor
    * @return Template -- objekt sebe
    */
   public function addCssFile($cssfile) {
      Template::addCss(Template::getFileDir($cssfile, Template::STYLESHEETS_DIR).$cssfile);
      return $this;
   }

   /*
    * ========== STATICKÉ METODY
    */

   /**
    * metoda přidává zadany css styl do výstupu
    * @param string -- název scc stylu
    * @param boolean -- true pokud je zadána i cesta se souborem
    */
   public static function addCss($cssName) {
   //TODO kontrola souborů
      if(!in_array($cssName, self::$stylesheets)) {
         array_push(self::$stylesheets, $cssName);
      }
   }

   /**
    * metoda přidává zadaný javascript do výstupu
    * @param string -- název javascriptu
    * @param boolean -- true pokud je zadána i cesta se souborem
    */
   public static function addJS($jsFile) {
   //TODO kontrola souborů
      if(!in_array($jsFile, self::$javascripts)) {
         array_push(self::$javascripts, $jsFile);
      }
   }

      /**
    * Metoda vrací pole se všemy css soubory
    * @return array
    */
   public static function getStylesheets() {
      return self::$stylesheets;
   }

   /**
    * Metoda vrací pole se všemi javascripty
    * @return array
    */
   public static function getJavascripts() {
      return self::$javascripts;
   }

   /**
    * Metoda vrací název adresáře s požadovaným souborem (bez souboru)
    * @param string $file -- název souboru
    * @param string $type -- typ adresáře - konstanta třídy
    * @param boolean $engine -- jestli se jedná o objekt enginu nebo modulu
    * @return string -- adresář bez souboru
    */
   public static function getFileDir($file, $dir = self::TEMPLATES_DIR) {
      $faceDir = './'.self::FACES_DIR.DIRECTORY_SEPARATOR
          .self::$face.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR;
      $mainDir = './'.$dir.DIRECTORY_SEPARATOR;
      // pokud existuje soubor ve vzhledu
      if(file_exists($faceDir.$file)) {
         return $faceDir;
      } else if(file_exists($mainDir.$file)) {
            return $mainDir;
         } else {
            trigger_error(sprintf(_('Soubor "%s" nebyl nalezen'), $file));
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
    * Metoda vrací adresář nebo název zvoleného vhledu
    * @param boolean $onlyName -- (option) jestli se má vrátit jen název (default: true)
    * @return string -- adresář vzhledu
    */
   final public static function face($onlyName = true) {
      if($onlyName) {
         return self::$face;
      } else {
         return Url_Request::getBaseWebDir().self::FACES_DIR.'/'.self::$face.'/';
      }
   }
}
?>