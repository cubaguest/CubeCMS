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
require_once 'template_outputfilters.php'; // filtry výstupu
require_once 'nonvve/browser/Browser.php'; // browser detection library

class Template extends TrObject {
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
    * Objekt s odkazem pro danou šablonu
    * @var Url_Link
    */
   protected $link = null;

   /**
    * Objekt identifikace prohlížeče
    * @var Browser
    */
   private static $browser;

   /*
    * ============= MAGICKÉ METODY
   */

   /**
    * Konstruktor třídy
    * @param Module_Sys $modulesys -- systémový objekt modulu a práv
    */
   public function __construct(Url_Link $link) {
      $this->link = $link;
      self::$browser = new Browser();
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
   public static function addPageTitle($text) {
      Template_Core::addToPageTitle($text);
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
    * Metoda vrací objat prohlížeče
    * @return Browser
    */
   public static function browser()
   {
      return self::$browser;
   }
   
   /**
    * Metoda přidá požadovaný soubor šablony do výstupu
    * @param string $name -- název souboru
    * @return Template
    */
   public function addTplFile($name) {
      $this->addFile('tpl://'.$name);
      return $this;

//       $file = self::getFileDir($name, self::TEMPLATES_DIR, true).$name;
//       if(!in_array($file, $this->templateFiles)){
//          array_push($this->templateFiles, $file);
//       }
   }

   /**
    * Magická metoda převede šablonu na řetězec
    * @return string -- vygenerovaný řetězec z šablon
    */
   public function  __toString() {
      if (defined('VVE_USE_GZIP') AND VVE_USE_GZIP == true AND
        isset ($_SERVER['HTTP_ACCEPT_ENCODING']) AND substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){
         ob_start("ob_gzhandler");
      } else {
         ob_start();
      }
      foreach ($this->templateFiles as $file) {
         try {
            if(is_file($file)) {
               include $file;
            }
         } catch(Exception $e) {
            new CoreErrors($e);
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
    * @param bool $parentFace -- (option) jestli se má vracet adresář k hlavnímu webu
    * @return string -- adresář vzhledu
    */
   final public static function faceDir($parentFace = false) {
      if($parentFace){
         return AppCore::getAppLibDir().self::FACES_DIR.DIRECTORY_SEPARATOR.self::face().DIRECTORY_SEPARATOR;
      }
      return AppCore::getAppWebDir().self::FACES_DIR.DIRECTORY_SEPARATOR.self::face().DIRECTORY_SEPARATOR;
   }
   
   /**
    * Metoda vrací URL adresu ke zvoleného vhledu
    * @param bool $parentFace -- (option) jestli se má vracet cesta k hlavnímu webu
    * @return string -- adresář vzhledu
    */
   final public static function faceUrl($parentFace = false) {
      if($parentFace){
         return Url_Request::getBaseWebDir(true).Template::FACES_DIR.'/'.Template::face()."/";
      }
      return Url_Request::getBaseWebDir(false).Template::FACES_DIR.'/'.Template::face()."/";
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
            Template::addJS((string)$file);
         } else if($file instanceof JsPlugin_CssFile) {
            Template::addCss((string)$file);
         } else {
            Template::addJS((string)$file);
         }
      }
   }

   /**
    * Metoda přidá javascript soubor do šablony
    * @param string/JsPlugin_JsFile $jsfile -- název souboru nebo objek JsPlugin_JsFile(pro virtuální)
    * @return Template -- objekt sebe
    */
   public function addJsFile($jsfile) {
      if(strncmp ($jsfile, 'http', 4) == 0){
         Template::addJS($jsfile);
      } else {
         $this->addFile('js://'.$jsfile);
      }
      return $this;
   }

   /**
    * Metoda přidá zadaný css soubor do stylů stránky
    * @param string $cssfile -- css soubor
    * @return Template -- objekt sebe
    */
   public function addCssFile($cssfile) {
      if(strncmp ($cssfile, 'http', 4) == 0){
         Template::addCss($cssfile);
      } else {
         $this->addFile('css://'.$cssfile);
      }
      return $this;
   }

   /**
    * metoda zkontroluje jestli je daná šablona už přidána
    * @param string $name -- název šablony
    * @return type 
    */
   public function haveTpl($name)
   {
      return isset ($this->templateFiles[$name]);
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
      else if(VVE_SUB_SITE_DIR != null AND $dir == self::TEMPLATES_DIR AND is_file(str_replace(AppCore::getAppWebDir(), AppCore::getAppLibDir(), $faceDir).$file)) {
            $return = str_replace(AppCore::getAppWebDir(), AppCore::getAppLibDir(), $faceDir);
      } else if(is_file($mainDir.$file)) {
         if($realpath) {
            $return = $mainDir;
         } else {
            $return = Url_Request::getBaseWebDir().$dir.URL_SEPARATOR;
         }
      } else {
         trigger_error(sprintf($this->tr('Soubor "%s %s" nebyl nalezen'), $dir, $file));
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
      self::$browser = new Browser();
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
    * Metoda vrací jestli je aktuální stránka titulní
    * @return bool -- true pokud se jedná o titulní stránku
    */
   final public static function isTitlePage() {
      return AppCore::getUrlRequest()->getCategory() == null ? true : false;
   }

   /**
    * Metoda pro přímé vložení souvboru do šablony
    * @param string $res -- zdroj souboru viz. addFile()
    */
   public function includeFile($res, $vars = null){
      $this->addFile($res, true, $vars);
   }

   /**
    * Metoda vloží požadovný soubor ze zadaného zdroje
    * @param string $resource
    * @todo dodělat a dořešit
    * Formáty:
    * tpl://file.phtml -- tpl file from current module
    * css://file.css -- css file from current module
    * js://file.js -- javascript file from current module
    * tpl://module:file.phtml -- tpl file from defined module
    * css://module:file.css -- css file from defined module
    * tpl://engine:file.phtml -- tpl file from engine
    * css://engine:file.css -- css file from engine
    * http://www.text.com/stylesheets/style.css -- externí css soubor
    *
    * Pokud je za souborem parametr "?original" vloží se originální soubor (ne z faces)
    */
   public function addFile($resource, $directInclude = false, $vars = null) {
      $matches = array();
      if(preg_match('/^(?P<res>tpl|css|js|http|https):\/\/(?:(?P<module>[a-z_-]+):)?(?P<filepath>(?:[a-z0-9_\/.-]*\/)?(?P<file>[^.]+\.(?P<ext>[^?#]+)))(?:[?#](?P<params>[a-z0-9_.=&#-]+))?$/i', $resource, $matches) == 1) {
         $original = false;
         if(isset ($matches['params']) AND $matches['params'] == 'original'){
            $original == true;
         }
         switch ($matches['res']) {
            case 'tpl':
               if($matches['module'] == null OR $matches['module'] == 'engine'){ // jedná se soubor s aktuálního modulu nebo s enginu
                  $filePath = $this->getTplPathFromEngine($matches['filepath'], $original);
               } else {
                  $filePath = $this->getTplPathFromModule($matches['filepath'], $matches['module'], $original);
               }
               if($directInclude == true){
                  include $filePath;
               } else {
                  $this->templateFiles[$matches['file']] = $filePath;
               }
               break;
            case 'css':
               $filePath = null;
               if($matches['ext'] == "less"){
                  if($matches['module'] == null OR $matches['module'] == 'engine'){ // jedná se soubor s aktuálního modulu nebo s enginu
                     $filePath = $this->getLesscCssFromEngine($matches['filepath'], $original);
                  } else {
                     $filePath = $this->getLesscCssFromModule($matches['filepath'], $matches['module'], $original);
                  }
               } else if($matches['ext'] == "scss"){
                  if($matches['module'] == null OR $matches['module'] == 'engine'){ // jedná se soubor s aktuálního modulu nebo s enginu
                     $filePath = $this->getSassCssFromEngine($matches['filepath'], $original, $matches['ext']);
                  } else {
                     $filePath = $this->getSassCssFromModule($matches['filepath'], $matches['module'], $original, $matches['ext']);
                  }
               } else {
                  if($matches['module'] == null OR $matches['module'] == 'engine'){ // jedná se soubor s aktuálního modulu nebo s enginu
                     $filePath = $this->getLinkPathFromEngine($matches['filepath'], self::STYLESHEETS_DIR, $original);
                  } else {
                     $filePath = $this->getLinkPathFromModule($matches['filepath'], $matches['module'], self::STYLESHEETS_DIR, $original);
                  }
               }
               Template::addCss($filePath);
               break;
            case 'js':
               if($matches['module'] == null OR $matches['module'] == 'engine'){ // jedná se soubor s aktuálního modulu nebo s enginu
                  $filePath = $this->getLinkPathFromEngine($matches['filepath'], self::JAVASCRIPTS_DIR, $original);
               } else {
                  $filePath = $this->getLinkPathFromModule($matches['filepath'], $matches['module'], self::JAVASCRIPTS_DIR, $original);
               }
               Template::addJs($filePath);
               break;
            default:
               // detekujeme koncovku souboru
               if(isset ($matches['ext']) AND ($matches['ext'] == 'css' OR $matches['ext'] == 'js')){
                  $func = 'add'.ucfirst($matches['ext']);
                  Template::$func($resource);
               }
               break;
         }

      } else {
         throw new UnexpectedValueException($this->tr('Nepodporovaný typ zdroje'));
      }
   }

   /**
    * Metoda přidá šablonu z enginu
    * @param <type> $file
    * @param <type> $original
    * @return absolutní cesta k souboru
    */
   protected function getTplPathFromEngine($file, $original = false) {
      $file = str_replace('/', DIRECTORY_SEPARATOR, $file);
      $faceDir = $parentFaceDir = Template::faceDir().self::TEMPLATES_DIR.DIRECTORY_SEPARATOR;
      if(VVE_SUB_SITE_DIR != null){
         $parentFaceDir = str_replace(AppCore::getAppWebDir(), AppCore::getAppLibDir(), $faceDir);
      }
      $mainDir = AppCore::getAppLibDir().self::TEMPLATES_DIR.DIRECTORY_SEPARATOR;

      if($original == false AND is_file($faceDir.$file)){ // soubor z face webu
         $path = $faceDir.$file;
      } else if($original == false AND VVE_SUB_SITE_DIR != null AND is_file($parentFaceDir.$file)) { // soubor z nadřazeného face (subdomains)
         $path = $parentFaceDir.$file;
      } else if(is_file($mainDir.$file)) { // soubor v knihovnách
         $path = $mainDir.$file;
      } else {
         throw new Template_Exception(sprintf($this->tr('Soubor "%s %s" nebyl nalezen'), $mainDir, $file));
      }
      return $path;
   }

   /**
    * Metoda přidá šablonu z modulu
    * @param <type> $file
    * @param <type> $module
    * @param <type> $original
    * @return absolutní cesta k souboru
    */
   protected function getTplPathFromModule($file, $module, $original = false) {
      $file = str_replace('/', DIRECTORY_SEPARATOR, $file);
      $faceDir = $parentFaceDir = Template::faceDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.self::TEMPLATES_DIR.DIRECTORY_SEPARATOR;
      if(VVE_SUB_SITE_DIR != null){
         $parentFaceDir = str_replace(AppCore::getAppWebDir(), AppCore::getAppLibDir(), $faceDir);
      }
      $mainDir = AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.self::TEMPLATES_DIR.DIRECTORY_SEPARATOR;
/*
$file:
string 'main.phtml' (length=10)
string 'panel.phtml' (length=10)

$faceDir:
string '/var/www/vve6/faces/default/modules/text/templates/' (length=56)
/var/www/vve6/subdomain/faces/default/modules/text/templates/

$parentFaceDir:
string '/var/www/vve6/faces/default/modules/text/templates/' (length=56)
/var/www/vve6/subdomain/faces/default/modules/text/templates/

$mainDir:
string '/var/www/vve6/modules/text/templates/' (length=42)
/var/www/vve6/modules/text/templates/
*/
//       Debug::log($file, $faceDir, $parentFaceDir, $mainDir);

      $path = null;
      if($original == false AND is_file($faceDir.$file)){ // soubor z face webu
         $path = $faceDir.$file;
      } else if($original == false AND VVE_SUB_SITE_DIR != null AND is_file($parentFaceDir.$file)) { // soubor z nadřazeného face (subdomains)
         $path = $parentFaceDir.$file;
      } else if(is_file($mainDir.$file)) { // soubor v knihovnách
         $path = $mainDir.$file;
      } else {
         throw new Template_Exception(sprintf($this->tr('Soubor "%s %s" nebyl nalezen'), $mainDir, $file));
      }
      return $path;
   }

   /**
    * Metoda vrací cestu k souboru CSS nebo JS
    * @param <type> $file
    * @param <type> $type -- typ souboru (jeho adresář) self::STYLESHEETS_DIR nebo self::JAVASCRIPTS_DIR
    * @param <type> $original
    */
   protected function getLinkPathFromEngine($file, $type = self::STYLESHEETS_DIR, $original = false) {
      $rpFile = str_replace('/', DIRECTORY_SEPARATOR, $file);
      $rpFaceDir = $rpParentFaceDir = Template::faceDir().$type.DIRECTORY_SEPARATOR;
      if(VVE_SUB_SITE_DIR != null){
         $rpParentFaceDir = str_replace(AppCore::getAppWebDir(), AppCore::getAppLibDir(), $rpFaceDir);
      }
      $rpMainDir = AppCore::getAppLibDir().$type.DIRECTORY_SEPARATOR;
      $path = null;

      if($original == false AND is_file($rpFaceDir.$rpFile)){ // soubor z face webu
         $path = Template::face(false).$type.'/'.$file;
      } else if($original == false AND VVE_SUB_SITE_DOMAIN != null AND is_file($rpParentFaceDir.$rpFile)) { // soubor z nadřazeného face (subdomains)
         $path = str_replace(Url_Request::getBaseWebDir(), Url_Request::getBaseWebDir(true), Template::face(false)).$type.'/'.$file;
      } else if(is_file($rpMainDir.str_replace('/', DIRECTORY_SEPARATOR, $file))) { // soubor v knihovnách
         if(VVE_SUB_SITE_DOMAIN == null){
            $path = Url_Request::getBaseWebDir().$type.'/'.$file;
         } else {
            $path = Url_Request::getBaseWebDir(true).$type.'/'.$file;
         }
      } else {
         throw new Template_Exception(sprintf($this->tr('Soubor "%s%s" nebyl nalezen'), $rpMainDir, $file));
      }
      return $path;
   }

   /**
    * Metoda vrací cestu k souboru CSS nebo JS
    * @param <type> $file
    * @param <type> $module
    * @param <type> $type -- typ souboru (jeho adresář) self::STYLESHEETS_DIR nebo self::JAVASCRIPTS_DIR
    * @param <type> $original
    */
   protected function getLinkPathFromModule($file, $module, $type = self::STYLESHEETS_DIR, $original = false) {
      $rpFile = str_replace('/', DIRECTORY_SEPARATOR, $file);
      $rpFaceDir = $rpParentFaceDir = Template::faceDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR;
      if(VVE_SUB_SITE_DIR != null){
         $rpParentFaceDir = str_replace(AppCore::getAppWebDir(), AppCore::getAppLibDir(), $rpFaceDir);
      }
      $rpMainDir = AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR;
      $path = null;

      if($original == false AND is_file($rpFaceDir.$rpFile)){ // soubor z face webu
         $path = Template::face(false).AppCore::MODULES_DIR.'/'.$module.'/'.$type.'/'.$file;
      } else if($original == false AND VVE_SUB_SITE_DOMAIN != null AND is_file($rpParentFaceDir.$rpFile)) { // soubor z nadřazeného face (subdomains)

         $path = str_replace(Url_Request::getBaseWebDir(), Url_Request::getBaseWebDir(true),Template::face(false)).AppCore::MODULES_DIR.'/'.$module.'/'.$type.'/'.$file;

      } else if(is_file($rpMainDir.$file)) { // soubor v knihovnách
         if(VVE_SUB_SITE_DOMAIN == null){
            $path = Url_Request::getBaseWebDir().AppCore::MODULES_DIR.'/'.$module.'/'.$type.'/'.$file;
         } else {
            $path = Url_Request::getBaseWebDir(true).AppCore::MODULES_DIR.'/'.$module.'/'.$type.'/'.$file;
         }
      } else {
         throw new Template_Exception(sprintf($this->tr('Soubor "%s%s" nebyl nalezen'), $rpMainDir, $file));
      }
      return $path;
   }

   protected function getLesscCssFromEngine($file, $original = false) {
      require_once AppCore::getAppLibDir()."lib".DIRECTORY_SEPARATOR."nonvve".DIRECTORY_SEPARATOR."lessphp".DIRECTORY_SEPARATOR."lessc.inc.php";
      $rpFile = str_replace('/', DIRECTORY_SEPARATOR, $file);
      $rpFaceDir = Template::faceDir().self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR;
      $rpParentFaceDir = Template::faceDir(true).self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR;
      
      if(VVE_SUB_SITE_DIR != null){
         $rpParentFaceDir = str_replace(AppCore::getAppWebDir(), AppCore::getAppLibDir(), $rpFaceDir);
      }
      $rpMainDir = AppCore::getAppLibDir().self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR;
      
      $path = $url = null;
      if($original == false AND is_file($rpFaceDir.$rpFile)){ // soubor z face webu
         $path = $rpFaceDir;
         $url = Template::face(false);
      } else if($original == false AND VVE_SUB_SITE_DOMAIN != null AND is_file($rpParentFaceDir.$rpFile)) { // soubor z nadřazeného face (subdomains)
         $path = $rpParentFaceDir;
         $url = str_replace(Url_Request::getBaseWebDir(), Url_Request::getBaseWebDir(true), Template::face(false));
      } else if(is_file($rpMainDir.$rpFile)) { // soubor v knihovnách
         $path = $rpMainDir;
         $url = Url_Request::getBaseWebDir(true);
      } else {
         throw new Template_Exception(sprintf($this->tr('Soubor "%s%s" nebyl nalezen'), $rpMainDir, $file));
      }
      
      try {
         if(VVE_DEBUG_LEVEL >= 2){
            $cssStr = lessc::cexecute($path . $rpFile, true);
            file_put_contents($path . $rpFile . ".css",  $cssStr['compiled']); // when debug > 2force recompile
         } else {
            lessc::ccompile($path . $rpFile, $path . $rpFile . ".css");
         }
      } catch (Exception $exc) {
         new CoreErrors($exc);
      }
      return $url.self::STYLESHEETS_DIR."/".$file.".css";
   }
   
   protected function getLesscCssFromModule($file, $module, $original = false) {
      require_once AppCore::getAppLibDir()."lib".DIRECTORY_SEPARATOR."nonvve".DIRECTORY_SEPARATOR."lessphp".DIRECTORY_SEPARATOR."lessc.inc.php";
      
//      $rpFile = str_replace('/', DIRECTORY_SEPARATOR, $file);
//      $rpFaceDir = Template::faceDir().self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR;
//      $rpParentFaceDir = Template::faceDir(true).self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR;
//      
//      if(VVE_SUB_SITE_DIR != null){
//         $rpParentFaceDir = str_replace(AppCore::getAppWebDir(), AppCore::getAppLibDir(), $rpFaceDir);
//      }
//      $rpMainDir = AppCore::getAppLibDir().self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR;
//      
//      $path = $url = null;
//      if($original == false AND is_file($rpFaceDir.$rpFile)){ // soubor z face webu
//         $path = $rpFaceDir;
//         $url = Template::face(false);
//      } else if($original == false AND VVE_SUB_SITE_DOMAIN != null AND is_file($rpParentFaceDir.$rpFile)) { // soubor z nadřazeného face (subdomains)
//         $path = $rpParentFaceDir;
//         $url = str_replace(Url_Request::getBaseWebDir(), Url_Request::getBaseWebDir(true), Template::face(false));
//      } else if(is_file($rpMainDir.$rpFile)) { // soubor v knihovnách
//         $path = $rpMainDir;
//         $url = Url_Request::getBaseWebDir(true);
//      } else {
//         throw new Template_Exception(sprintf($this->tr('Soubor "%s%s" nebyl nalezen'), $rpMainDir, $file));
//      }
//      
//      lessc::ccompile($path . $rpFile, $path . $rpFile.".css");
//      return $url.self::STYLESHEETS_DIR."/".$file.".css";
   }
   
   protected function getSassCssFromEngine($file, $original = false, $ext = 'scss') {
      require_once AppCore::getAppLibDir()."lib".DIRECTORY_SEPARATOR."nonvve".DIRECTORY_SEPARATOR."phpsass".DIRECTORY_SEPARATOR."SassParser.php";
      $rpFile = str_replace('/', DIRECTORY_SEPARATOR, $file);
      $rpFaceDir = Template::faceDir().self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR;
      $rpParentFaceDir = Template::faceDir(true).self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR;
      
      if(VVE_SUB_SITE_DIR != null){
         $rpParentFaceDir = str_replace(AppCore::getAppWebDir(), AppCore::getAppLibDir(), $rpFaceDir);
      }
      $rpMainDir = AppCore::getAppLibDir().self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR;
      
      $path = $url = null;
      if($original == false AND is_file($rpFaceDir.$rpFile)){ // soubor z face webu
         $path = $rpFaceDir;
         $url = Template::face(false);
      } else if($original == false AND VVE_SUB_SITE_DOMAIN != null AND is_file($rpParentFaceDir.$rpFile)) { // soubor z nadřazeného face (subdomains)
         $path = $rpParentFaceDir;
         $url = str_replace(Url_Request::getBaseWebDir(), Url_Request::getBaseWebDir(true), Template::face(false));
      } else if(is_file($rpMainDir.$rpFile)) { // soubor v knihovnách
         $path = $rpMainDir;
         $url = Url_Request::getBaseWebDir(true);
      } else {
         throw new Template_Exception(sprintf($this->tr('Soubor "%s%s" nebyl nalezen'), $rpMainDir, $file));
      }
      
      try {
         $options = array(
            'style' => SassRenderer::STYLE_NESTED,
            'cache' => FALSE,
            'syntax' => $ext == "sass" ? SassFile::SASS : SassFile::SCSS,
            'debug' => false,
            'debug_info' => false,
         );
         $parser = new SassParser($options);
         file_put_contents($path . $rpFile . ".css",  $parser->toCss($path . $rpFile)); // when debug > 2force recompile
      } catch (Exception $exc) {
         new CoreErrors($exc);
      }
      return $url.self::STYLESHEETS_DIR."/".$file.".css";
   }
   
   protected function getSassCssFromModule($file, $module, $original = false, $ext = 'scss') {
      require_once AppCore::getAppLibDir()."lib".DIRECTORY_SEPARATOR."nonvve".DIRECTORY_SEPARATOR."phpsass".DIRECTORY_SEPARATOR."SassParser.php";
      
//      $rpFile = str_replace('/', DIRECTORY_SEPARATOR, $file);
//      $rpFaceDir = Template::faceDir().self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR;
//      $rpParentFaceDir = Template::faceDir(true).self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR;
//      
//      if(VVE_SUB_SITE_DIR != null){
//         $rpParentFaceDir = str_replace(AppCore::getAppWebDir(), AppCore::getAppLibDir(), $rpFaceDir);
//      }
//      $rpMainDir = AppCore::getAppLibDir().self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR;
//      
//      $path = $url = null;
//      if($original == false AND is_file($rpFaceDir.$rpFile)){ // soubor z face webu
//         $path = $rpFaceDir;
//         $url = Template::face(false);
//      } else if($original == false AND VVE_SUB_SITE_DOMAIN != null AND is_file($rpParentFaceDir.$rpFile)) { // soubor z nadřazeného face (subdomains)
//         $path = $rpParentFaceDir;
//         $url = str_replace(Url_Request::getBaseWebDir(), Url_Request::getBaseWebDir(true), Template::face(false));
//      } else if(is_file($rpMainDir.$rpFile)) { // soubor v knihovnách
//         $path = $rpMainDir;
//         $url = Url_Request::getBaseWebDir(true);
//      } else {
//         throw new Template_Exception(sprintf($this->tr('Soubor "%s%s" nebyl nalezen'), $rpMainDir, $file));
//      }
//      
//      lessc::ccompile($path . $rpFile, $path . $rpFile.".css");
//      return $url.self::STYLESHEETS_DIR."/".$file.".css";
   }

   /**
    * Metoda vrací všechny proměnné šablony
    * @return array
    */
   public function getTemplateVars(){
      return  $this->privateVars;
   }

   /**
    * Metoda provede filtraci podle zadaných filtrů
    * @param string $text -- content
    * @param array/string $filters -- pole filtrů
    * @return string -- přefiltrovaný text
    */
   public function filter($text, $filters){
      if(is_string($filters)) $filters = array($filters);
      foreach ($filters as $filter) {
         if(function_exists('vve_filter_'.$filter)){
            $filter = 'vve_filter_'.$filter;
         } else if(function_exists($filter)){
         } else {
            throw new BadFunctionCallException($this->tr('Volán nedefinovaný výstupní filtr.'));
         }
          if(is_array($filter)){
            $text = $filter[0]($text, $this->link, $this, $filter[1]);
         } else {
            $text = $filter($text, $this->link, $this);
         }
      }

      return $text;
   }
}
?>
