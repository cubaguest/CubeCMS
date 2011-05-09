<?php
/**
 * Třída pro práci s šablonami modulu.
 * Třida obsahuje vrstvu mezi šablonovacím systémem a samotným pohledem (viewrem)
 * modulu. Umožňuje všechny základní operace při volbě a plnění šablony a jejímu
 * zobrazení v modulu.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: template_module.class.php 646 2009-08-28 13:44:00Z jakub $ VVE3.9.4 $Revision: 646 $
 * @author        $Author: jakub $ $Date: 2009-08-28 15:44:00 +0200 (Pá, 28 srp 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-08-28 15:44:00 +0200 (Pá, 28 srp 2009) $
 * @abstract 		Třída pro obsluhu šablony
 */


class Template_Module extends Template {
/**
 * Objekt pro lokalizaci v modulu
 * @var Locales
 */
   protected $locale = null;

   /**
    * Objekt kategorie pro kterou je šablona tvořena
    * @var Category
    */
   private $category = null;

   /**
    * Jestli je daná stránka editační
    * @var bool
    */
   private static $isEdidting = false;

   /**
    * KOnstruktor vytvoří objekt šablony pro modul
    * @param Url_Link_Module $link -- objekt odkazu
    * @param Category $category -- objekt kategorie
    */
   function  __construct(Url_Link_Module $link, Category_Core $category) {
      parent::__construct($link);
      $this->category = $category;
      $this->locale = new Locales($category->getModule()->getName());
   }

   /**
    * Metoda vrací objekt lokalizace
    * @return Locales
    */
   final public function locale() {
      return $this->locale;
   }

   /**
    * Metoda přeloží zadaný řetězec
    * @param string $message -- řetězec k přeložení
    * @return string -- přeložený řetězec
    */
   final public function _($message) {
      return $this->locale()->_($message);
   }

   /**
    * Metoda vrací objekt kategorie
    * @return Category
    */
   public function category() {
      return $this->category;
   }

   /**
    * Metoda přidá požadovaný soubor šablony do výstupu
    * @param string $name -- název souboru
    * @param boolean $type -- jestli se jedná o šablonu enginu nebo název modulu
    */
   public function addTplFile($name, $type = null) {
      if($type === null){
         $this->addFile('tpl://'.$name);
      } else if($type === true){
         $this->addFile('tpl://engine:'.$name);
      } else if(is_string($type)){
         $this->addFile('tpl://'.$type.':'.$name);
      }
      return $this;
   }

   /**
    * Metoda přidá javascript soubor do šablony
    * @param string/JsPlugin_JsFile $jsfile -- název souboru nebo objek JsPlugin_JsFile(pro virtuální)
    * @return Template -- objekt sebe
    */
   public function addJsFile($jsfile, $engine = false) {
      if($engine){
         parent::addJsFile('engine:'.$jsfile);
      } else {
         parent::addJsFile($this->category()->getModule()->getName().':'.$jsfile);
      }
      return $this;
   }

   /**
    * Metoda přidá zadaný css soubor do stylů stránky
    * @param string $cssfile -- css soubor
    * @return Template_Module -- objekt sebe
    */
   public function addCssFile($cssfile, $engine = false) {
      if($engine){
         parent::addCssFile('engine:'.$cssfile);
      } else {
         parent::addCssFile($this->category()->getModule()->getName().':'.$cssfile);
      }
      return $this;
   }

   /**
    * Metoda vloží šablonu na zadané místo
    * @param string $name -- název šablony
    * @param boolean $engine -- (option) jestli se jedná o šablonu enginu
    */
   public function includeTpl($name, $engine = false, $vars = null, $module = null) {
      if($module === null) $module = $this->category()->getModule()->getName();
      if($engine){
         $this->includeFile('tpl://engine:'.$name);
      } else {
         $this->includeFile('tpl://'.$module.':'.$name);
      }
      unset ($vars);
   }

   /**
    * Metoda vrací název adresáře s požadovaným souborem (bez souboru)
    * @param string $file -- název souboru
    * @param string $type -- typ adresáře - konstanta třídy
    * @param boolean $engine -- jestli se jedná o objekt enginu nebo modulu
    * @return string -- adresář bez souboru
    */
   public static function getFileDir($file, $dir = self::TEMPLATES_DIR, $moduleName = null, $realpath = false, $withFile = false) {
      $faceDir = AppCore::getAppWebDir().self::FACES_DIR.DIRECTORY_SEPARATOR.self::$face.DIRECTORY_SEPARATOR
          .AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR
          .$dir.DIRECTORY_SEPARATOR;
      $mainDir = AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR
          .$dir.DIRECTORY_SEPARATOR;
      // pokud existuje soubor ve vzhledu
      $ret = null;
      if(file_exists($faceDir.$file)) {
          if($realpath){
             $ret = $faceDir;
          } else {
             $ret = Url_Request::getBaseWebDir().self::FACES_DIR.URL_SEPARATOR.self::$face
             .URL_SEPARATOR.AppCore::MODULES_DIR.URL_SEPARATOR.$moduleName.URL_SEPARATOR.$dir.URL_SEPARATOR;
          }
      }
      // pokud se šablona loaduje z jiného faces (např nadřazeného webu)
      else if(VVE_USE_SUBDOMAIN_HTACCESS_WORKAROUND != null AND $dir == self::TEMPLATES_DIR
              AND file_exists(str_replace(VVE_USE_SUBDOMAIN_HTACCESS_WORKAROUND, null, $faceDir).$file)) {
            $ret = str_replace(VVE_USE_SUBDOMAIN_HTACCESS_WORKAROUND, null, $faceDir);
      } else if(file_exists($mainDir.$file)) {
         if($realpath){
            $ret = $mainDir;
         } else {
            return Url_Request::getBaseWebDir().AppCore::MODULES_DIR.URL_SEPARATOR.$moduleName.URL_SEPARATOR
          .$dir.URL_SEPARATOR;
            $ret = null;
         }
      } else {
         trigger_error(sprintf(_('Soubor "%s" s šablonou v modulu "%s" nebyl nalezen'),
               $file, $moduleName));
      }
      if($withFile == true){
         $ret .= $file;
      }
      return $ret;
   }

   /**
    * Magická metoda převede šablonu na řetězec
    * @return string -- vygenerovaný řetězec z šablon
    */
   public function  __toString() {
//       ob_start();
//       var_dump($this);
//       $hash = md5(ob_get_clean());
//       $fileTmp = AppCore::getAppCacheDir().$hash.'.html';
//       if(!is_file($fileTmp)){
         // zastavení výpisu buferu
         ob_start();
         ob_clean();
         foreach ($this->templateFiles as $file) {
            if(file_exists($file)){
               try {
                  // jaký modul je vkládán a podle toho se změní locales
                  $strpos = strpos($file, 'modules/')+8;
                  $module = substr($file, $strpos, strpos($file, "/templates") - $strpos);
                  $this->locale()->setDomain($module);
                  include $file;
               } catch (Exception $e) {
                  new CoreErrors($e);
               }
            }
         }
         $contents = ob_get_clean();
//          file_put_contents($fileTmp, $contents, LOCK_EX);
//       } else {
//          $contents = file_get_contents($fileTmp);
//       }
      return $contents;
   }

   /**
    * Metoda přidá soubor do šablony
    * @param <type> $resource
    * @param <type> $directInclude
    */
   public function  addFile($resource, $directInclude = false) {
      // přidání názvu modulu do tpl pokud tam není
      parent::addFile(preg_replace('/^(tpl|css|js):\/\/(?![a-z]+:)/i', '\\1://'.$this->category()->getModule()->getName().':' , $resource), $directInclude);
   }

   /**
    * Metoda vrací jestli se daná stránka edituje
    * @return bool
    */
   public static function isEdit() {
      return self::$isEdidting;
   }

   /**
    * Metoda nastavuje jestli se edituje
    * @param bool $edit -- true pro zapnutí
    */
   public static function setEdit($edit = false) {
      self::$isEdidting = $edit;
   }
}
?>