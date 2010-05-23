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
require_once 'template_postfilters.php';

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
    * Proměná s názvem nadpisu
    * @var array
    */
   protected static $pageHeadline = null;

   /**
    * Proměná s názvem titulku okna
    * @var array
    */
   protected static $pageTitle = array();

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
   public static function setPVar($name, $value) {
      self::$publicVars[$name] = $value;
   }

   /**
    * Metoda vrací hodnotu veřejné proměnné
    * @param string $name -- název proměnné
    * @return mixed -- hodnota proměnné
    */
   public static function pVar($name) {
      if (isset (self::$publicVars[$name])) {
         return self::$publicVars[$name];
      }
      return null;
   }

   /**
    * Metoda nastaví název titulku pro kategorii
    * @param string $text -- název kategorie
    * @deprecated -- použít setPageHeadline protože se hodnota nastavuje, nepřidává !
    */
   final public static function addPageHeadline($text) {
//      array_push(self::$pageHeadline, $text);
      self::$pageHeadline = $text;
   }

   /**
    * Metoda nastaví název titulku stránky
    * @param string $text -- název stránky
    */
   final public static function setPageHeadline($text) {
//      array_push(self::$pageHeadline, $text);
      self::$pageHeadline = $text;
   }

   /**
    * Metoda přidá text k titulku pro článek
    * @param string $text -- název článek
    */
   final public static function addPageTitle($text) {
      array_push(self::$pageTitle, $text);
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
      $file = self::getFileDir($name, self::TEMPLATES_DIR, true).$name;
      if(!in_array($file, $this->templateFiles)){
         array_push($this->templateFiles, $file);
      }
   }

   /**
    * Magická metoda převede šablonu na řetězec
    * @return string -- vygenerovaný řetězec z šablon
    */
   public function  __toString() {
      ob_start();
//      $this->renderTemplate();
      foreach ($this->templateFiles as $file) {
         if(file_exists($file)) {
//            try {
               include $file;
//            } catch (Exception $e) {
//               new CoreErrors($e);
//            }
         }
      }
      $cnt = ob_get_clean();
      return $cnt;
   }


   /**
    * Metoda vykreslí danou šablonu a její výsledek odešle na výstup
    */
   public function renderTemplate() {
      // zastavení výpisu buferu
      echo ($this);
   }

   /**
    * Metoda vloží šablonu na zadané místo
    * @param string $name -- název šablony
    * @param boolean $engine -- (option) jestli se jedná o šablonu enginu
    */
   public function includeTpl($name, $engine = false, $vars = null) {
      $path = self::getFileDir($name, self::TEMPLATES_DIR, true);
      include $path.$name;
      unset ($vars);
   }

   /**
    * Metoda vrací adresář zvoleného vhledu (absolutní)
    * @return string -- adresář vzhledu
    */
   final public static function faceDir() {
      return AppCore::getAppWebDir().self::FACES_DIR.DIRECTORY_SEPARATOR.self::face().DIRECTORY_SEPARATOR;
   }

   /**
    * Metoda vloží do šablony zadaný objekt šablony a vyrenderuje jej zvlášť,
    * nezávisle na objektu nadřízeném
    * @param Template $tplObj -- objekt šablony
    * @param array $vars -- pole s proměnýmy, přenesenými přímo do objektu
    */
   final public function includeTplObj(Template $tplObj, $vars = array()) {
      foreach ($vars as $key => $value) {
         $tplObj->{$key} = $value;
      }
      $tplObj->renderTemplate();
   }

   /**
    * Metoda přidá do šablony objekt JsPluginu
    * @param JsPlugin $jsplugin -- objekt JsPluginu
    */
   final static public function addJsPlugin(JsPlugin $jsplugin) {
      $jsfiles = $jsplugin->getAllFiles();
      foreach ($jsfiles as $file) {
         if($file instanceof JsPlugin_JsFile) {
            Template::addJS($file);
         } else if($file instanceof JsPlugin_CssFile) {
            // pokud existuje css soubor u faces, vložíme ten
            if(file_exists(AppCore::getAppWebDir().self::FACES_DIR.DIRECTORY_SEPARATOR.Template::face(true)
            .DIRECTORY_SEPARATOR.self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR.$file->getName(false))) {
               Template::addCss(Url_Request::getBaseWebDir().self::FACES_DIR.DIRECTORY_SEPARATOR.Template::face(true).DIRECTORY_SEPARATOR.self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR.$file->getName(false));
            } else {
               Template::addCss($file->getName());
            }
         } else {
            Template::addJS($file);
         }
      }
   }

   /**
    * Metoda přidá javascript soubor do šablony
    * @param string/JsPlugin_JsFile $jsfile -- název souboru nebo objek JsPlugin_JsFile(pro virtuální)
    * @return Template -- objekt sebe
    */
   public function addJsFile($jsfile) {
      //konttrola jestli se nejedná o URL adresu (vzdálený soubor)
      if(preg_match('/^http[s]?:\/\//', $jsfile)){
         Template::addJS($jsfile);
      } else {
         $filePath = Template::getFileDir($jsfile, Template::JAVASCRIPTS_DIR, false);
         if($filePath != null) {
            Template::addJS($filePath.$jsfile);
         }
      }
      return $this;
   }

   /**
    * Metoda přidá zadaný css soubor do stylů stránky
    * @param string $cssfile -- css soubor
    * @return Template -- objekt sebe
    */
   public function addCssFile($cssfile) {
      $filePath = Template::getFileDir($cssfile, Template::STYLESHEETS_DIR, false);
      if($filePath != null) {
         Template::addCss($filePath.$cssfile);
      }
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
    * @todo přepsat kontroly, protože tohle je dost velký bordel to samé i Template_Modules
    */
   public static function getFileDir($file, $dir = self::TEMPLATES_DIR, $realpath = false, $withFile = false) {
      $faceDir =  AppCore::getAppWebDir().self::FACES_DIR.DIRECTORY_SEPARATOR
              .self::$face.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR;
      $mainDir = AppCore::getAppLibDir().$dir.DIRECTORY_SEPARATOR;
      $return = null;
      // pokud existuje soubor ve vzhledu
      if(file_exists($faceDir.$file)) {
         if($realpath) {
            $return = $faceDir;
         } else {
            $return = Url_Request::getBaseWebDir().self::FACES_DIR.URL_SEPARATOR.self::$face.URL_SEPARATOR.$dir.URL_SEPARATOR;
         }
      } // pokud se šablona loaduje z jiného faces (např nadřazeného webu)
      else if(VVE_USE_SUBDOMAIN_HTACCESS_WORKAROUND != null AND $dir == self::TEMPLATES_DIR
              AND file_exists(str_replace(VVE_USE_SUBDOMAIN_HTACCESS_WORKAROUND, null, $faceDir).$file)) {
            $return = str_replace(VVE_USE_SUBDOMAIN_HTACCESS_WORKAROUND, null, $faceDir);
      } else if(file_exists($mainDir.$file)) {
         if($realpath) {
            $return = $mainDir;
         } else {
            $return = Url_Request::getBaseWebDir().$dir.URL_SEPARATOR;
         }
      } else {
         trigger_error(sprintf(_('Soubor "%s" nebyl nalezen'), $file));
      }
      if($withFile == true){
         $return .= $file;
      }
      return $return;
   }

   /**
    * Metoda pro základní nasatvení šablonovacího systému
    */
   public static function factory() {
      self::setFace(VVE_TEMPLATE_FACE);
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

   /**
    * Metoda vloží požadovný soubor ze zadanéého zdroje
    * @param string $resource
    * @todo dodělat a dořešit
    */
   public function addFile($resource) {
      /*
       * Formáty:
       * tpl://file -- tpl file from current module
       * css://file -- css file from current module
       * js://file -- javascript file from current module
       * tpl://module/file -- tpl file from defined module
       * css://module/file -- css file from defined module
       *
      */
      $matches = array();
      if(preg_match('/^(?:(?P<res>[a-z]+):\/\/)(?:(?P<module>[a-z.]+)\/)?(?:(?P<file>[a-z.]+))/', $resource, $matches)) {
         switch ($matches['res']) {
            case 'tpl':
               
               break;
            case 'css':
            
               break;
            case 'js':
            
               break;
            default:
               break;
         }
         
      } else {
         throw new UnexpectedValueException(_('Nepodporovaný typ zdroje'));
      }
   }

   /**
    * Metoda vrací všechny proměnné šablony
    * @return array
    */
   public function getTemplateVars(){
      return  $this->privateVars;
   }
}
?>