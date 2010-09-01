<?php
/**
 * Třída pro práci s odkazy.
 * Třída pro tvorbu a práci s odkazy aplikace, umožňuje jejich pohodlnou
 * tvorbu a validaci, popřípadě změnu jednotlivých parametrů. Umožňuje také
 * přímé přesměrování na zvolený (vytvořený) odkaz pomocí klauzule redirect.
 *
 * @copyright  	Copyright (c) 2009 Jakub Matas
 * @version    	$Id: url_link.class.php 646 2009-08-28 13:44:00Z jakub $ VVE3.9.4 $Revision: 646 $
 * @author			$Author: jakub $ $Date: 2009-08-28 15:44:00 +0200 (Pá, 28 srp 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-08-28 15:44:00 +0200 (Pá, 28 srp 2009) $
 * @abstract 		Třída pro práci s odkazy
 */

class Url_Link {
/**
 * Oddělovač prametrů odkazu
 * @var string
 */
   const URL_PARAMETRES_SEPARATOR = '&amp;';

   /**
    * Oddělovač prametrů odkazu a samotného odkazu
    * @var string
    */
   const URL_SEPARATOR_LINK_PARAMS = '?';

   /**
    * Oddělovač parametrů v url
    * @var string
    */
   const URL_PARAMETRES_SEPARATOR_IN_URL = '&';

   /**
    * Oddělovač parametr/hodnota
    * @var string
    */
   const URL_SEP_PARAM_VALUE = '=';

   /**
    * Proměná s typem přenosového protokolu
    * @var string
    */
   protected static $user_transfer_protocol = null;

   /**
    * Proměnná s názvem jazyka
    * @var string
    */
   protected static $currentlang = null;

   /**
    * Proměnná s názvem jazyka
    * @var string
    */
   protected $lang = null;

   /**
    * Proměnná s názvem kategorie
    * @var string
    */
   protected $category = null;

   /**
    * Aktuálně nastavená kategorie
    * @var string
    */
   protected static $currentCategory = null;

   /**
    * Proměná se zvolenou cestou
    * @var string
    */
   protected $route = null;

   /**
    * Aktuálně zvolená cesta
    * @var string
    */
   protected static $currentRoute = null;

   /**
    * Pole s parsovatelnými parametry v url
    * @var array
    */
   protected $paramsArray = array();

   /**
    * Pole s pparametry v aktuální URL
    * @var array
    */
   protected static $currentParams = array();

   /**
    * Soubor který se má zobrazit
    * @var string
    */
   protected $file = null;

   /**
    * Soubor který se má zobrazit
    * @var string
    */
   protected static $currentFile = null;

   /**
    * Konstruktor nastaví základní adresy a přenosový protokol
    * @param boolean $clear -- (option) true pokud má být vrácen čistý link jenom s kategorií(pokud je vybrána) a jazykem
    * @param boolean $onlyWebRoot -- (option) true pokud má být vráce naprosto čistý link (web root)
    */
   function __construct($clear = false) {
   //      $this->onlyWebRoot = $onlyWebRoot;
      $this->_init();
      if($clear) {
         $this->clear();
      }
   }

   /*
    * STATICKÉ METODY
    */

   /**
    * Metoda nastavuje transportní protokol
    * @param string -- přenosový protokol (např. http://)
    */
   public static function setTransferProtocol($protocol) {
      self::$user_transfer_protocol = $protocol;
   }

   /**
    * Metoda vrací přenosový protokol
    * @return string -- přenosový protokol
    */
   public static function getTransferProtocol() {
      if(self::$user_transfer_protocol == null) {
      //         return Url_Request::TRANSFER_PROTOCOL;
      } else {
         return self::$user_transfer_protocol;
      }
   }

   /**
    * Metoda vrací adresu k web aplikaci
    * @return string -- adresa ke kořenu aplikace
    */
   public static function getMainWebDir() {
      return Url_Request::getBaseWebDir();
   }

   /*
    * VEŘEJNÉ METODY
    */

   /**
    * Metoda nastavuje název a id kategorie
    * @param string -- klíč kategorie
    *
    * @return Links -- objket Links
    */
   public function category($catKey = null) {
      if($catKey != null) {
//         $this->category = vve_cr_url_key($catKey);
         $this->category = $catKey;
      } else {
         $this->category = null;
      }
      return $this;
   }

   /**
    * Metoda nastaví aktuální cestu (routu)
    * @param string $route -- aktuální cesta
    */
   public static function setRoute($route) {
      self::$currentRoute = $route;
   }

   /**
    * Metoda nastavuje název a id routy
    * @param string -- název cesty
    * @param array -- pole s parametry pojmenovanými podle cesty
    *
    * @return Links -- objket Links
    */
//   public function route($name = null, $params = array()) {
//      return $this;
//   }

   /**
    * Metoda nastavuje typ media
    * @param string -- jméno media
    *
    * @return Links -- objket Links
    */
   public function media($media = null) {
      $this->mediaType = $media;
      return $this;
   }

   /**
    * Metoda nastavuje název lokalizace
    * @param string -- jméno jazyka (action např. cs, en, de atd.)
    *
    * @return Links -- objket Links
    */
   public function lang($lang = null) {
      $this->lang = $lang;
      return $this;
   }

   /**
    * Metoda přidá nebo změní daný parametr v URL
    * @param string $name -- objekt UrlParam nebo string
    * @param string $value -- (option) hodnota parametru, pokud je null bude parametr odstraněn
    */
   public function param($name, $value = null) {
      if($value !== null) {
         if(!is_array($name)) {
            $this->paramsArray[$name] = $value;
         } else {
            foreach ($name as $key => $val) {
               $this->paramsArray[$key] = $val;
            }
         }
      } else {
         $this->rmParam($name);
      }
      return $this;
   }

   /**
    * Metoda odstraní daný parametr z url
    * @param mixed $name -- (option) název parametru, který se má odstranit nebo
    * objekt UrlParam. Pokud zůstane nezadán, odstraní se všechny parametry
    *
    * @return Links
    */
   public function rmParam($name = null) {
      if($name != null) {
         unset($this->paramsArray[$name]);
      }
      // Odstranění všch parametrů (normálových i obyčejných)
      else {
         $this->paramsArray = array();
      }
      return $this;
   }

   /**
    * Metoda vrací parametr z url adresy
    * @param string $name -- název parametru
    * @param mixed $defValue -- výchozí hodnota parametru
    * @return mixed -- hodnota parametru
    */
   public function getParam($name, $defValue = null) {
      if(isset ($_GET[$name])){
         return urldecode($_GET[$name]);
      } else {
         return $defValue;
      }
   }

   /**
    * Metoda nastaví aktuální soubor
    * @param string $file -- název souboru
    */
   public function file($file = null) {
      $this->file = $file;
      return $this;
   }

   /**
    * Metoda nastaví aktuální soubor (alias pro file($file))
    * @param string $file -- název souboru
    */
   public function setFile($file = null) {
      return $this->file($file);
   }

   /**
    * Metoda nastaví parametry (přepíše původní)
    * @param string/array $params -- pole nebo řetězec parametrů
    */
   public static function setParams($params) {
      if(is_array($params)) {
         self::$currentParams = $params;
      } else {
         self::$currentParams = self::parseParams($params);
      }
   }

   /**
    * Metoda nastaví aktuální kategorii
    * @param string $catKey -- název kategorie
    */
   public static function setCategory($catKey) {
      self::$currentCategory = $catKey;
   }

   /**
    * Metoda nastaví aktuální jazyk
    * @param string $lang -- aktuální jazyk
    */
   public static function setLang($lang) {
      if(Locales::getDefaultLang() != $lang){
         self::$currentlang = $lang;
      }
   }

   /**
    * Metoda nastavuje znovunahrání stránky
    * @param string -- externí odkaz na který se má přesměrovat (option)
    */
   public function reload($link = null, $code = 302) {
      if(Url_Request::isXHRRequest()){ // u XHR není nutný reload
      } else if (!headers_sent()) {
         if($link == null) {
            header("Location: ".(string)$this, true, $code);
         } else {
            header("Location: ".(string)$link, true, $code);
         }
         exit;
      } else {
         throw new Exception(_("Hlavičky stránky byly již odeslány"));
         flush();
      }
   }

   /**
    * Metoda odstraní všechny parametry v odkazu
    * @return Links -- sám sebe
    */
   public function clear($withOutCategory = false) {
      $this->rmParam();
      $this->file(null);
      $this->route = null;
      if($withOutCategory) {
         $this->category();
      }
      return $this;
   }

   /*
    * PRIVÁTNÍ METODY
    */

   /**
    * Metoda inicializuje odkazy
    *
    */
   protected function _init() {
      $this->lang = self::$currentlang;
      $this->category = self::$currentCategory;
      $this->route = self::$currentRoute;
      $this->file = self::$currentFile;
      $this->paramsArray = self::$currentParams;
   }

   /*
    * MAGICKÉ METODY
    */

   /**
    * Metoda převede objekt na řetězec
    *
    * @return string -- objekt jako řetězec
    */
   public function __toString() {
      $returnString = Url_Request::getBaseWebDir();
      if($this->lang != null) {
         $returnString.=$this->getLang();
      }
      if($this->category != null) {
         $returnString.=$this->getCategory();
      }      
      if($this->getRoute() != null) {
         $returnString.=$this->getRoute();
      }
      if($this->file != null) {
         $returnString.=$this->getFile();
      }
      //        Parsovatelné parametry
      if(!empty ($this->paramsArray)) {
         $returnString.=$this->getParams();
      }
      $returnString = $this->repairUrl($returnString);
      return $returnString;
   }

   /**
    * Metoda parsuje normálové parametry a vrací je jako pole, kde klíč je název
    * a hodnota parametru je hodnota
    * @param string $params -- řetězec s parametry
    * @return array -- pole s parametry
    */
   protected static function parseParams($params) {
      $paramsArr= array();
      // odstrannění otazníku na začátku
      if($params != null) {
         $paramsArr = $_GET;
         if(!function_exists('urlDecodeParam')){
            function urlDecodeParam(&$param, $key) {
               $param = urldecode($param);
            }
         }
         array_walk_recursive($paramsArr, 'urlDecodeParam');
      }
      return $paramsArr;
   }

   /**
    * Metoda vrací část s parametry pro url (parsovatelné)
    * @param string -- řetězec s parametry
    */
   protected function getParams() {
      $return = null;
      if(!empty ($this->paramsArray)) {
         $return = self::URL_SEPARATOR_LINK_PARAMS.http_build_query($this->paramsArray);
      }
      return $return;
   }

   /**
    * Metoda vrací část lang pro url
    * @param string -- lang
    */
   protected function getLang() {
      if($this->lang != null) {
         return $this->lang.Url_Request::URL_SEPARATOR;
      } else {
         return null;
      }
   }

   /**
    * Metoda vrací část s kategorií pro url
    * @param string -- kategorie
    */
   protected function getCategory() {
      if($this->category != null) {
         if(strpos($this->category,'.') === false){
            return $this->category.Url_Request::URL_SEPARATOR;
         }
         return $this->category;
      } else {
         return null;
      }
   }

   /**
    * Metoda vrací část s cestou pro url
    * @param string -- cesta (routa)
    */
   protected function getRoute() {
      if($this->route != null) {
         return $this->route;
      } else {
         return null;
      }
   }

   /**
    * Metoda vrací část se souborem pro url
    * @param string -- soubor
    */
   protected function getFile() {
      if($this->file != null) {
         return $this->file;
      } else {
         return null;
      }
   }

   /**
    * Metoda odtraní z url špatné znaky a opakování
    * @param string $url -- url adresa
    * @todo ověřit nutnost, popřípadě vyřešit jinak protože na začátku adresy jsou
    * vždy dvě lomítka viz. http://
    */
   protected function repairUrl($url) {
//      $url = vve_cr_url_key($url);
//      $url = preg_replace("/\/{2,}/", "/", $url); // TODO ověřit nutnost
      return $url;
   }

   /**
    * Metoda inicializuje zpětný odkaz, kde bude tato metoda volána je považován za kořenový kontroller
    */
   public function backInit(Url_Link $link = null) {
      unset ($_SESSION['linkBack'][$this->category]);
      if(!isset ($_SESSION['linkBack'])) $_SESSION['linkBack'] = array();
      $_SESSION['linkBack'][$this->category] = array();
      if($link == null){
         $this->setBack($this);
      } else {
         $this->setBack($link);
      }
   }

   /**
    * Metoda uloží daný odkaz pod daný level
    * @param Url_LInk $link -- objekt odkazu
    * @param int $appLevel (option) level (def: 0)
    */
   private function setBack(Url_Link $link, $appLevel = 0) {
      $_SESSION['linkBack'][$this->category][$appLevel] = $link;
      ksort($_SESSION['linkBack'][$this->category]);
   }

   /**
    * Metoda vytvoří nový zpětný odkaz a vrátí předešlý odkaz s menším levelem
    * @param Url_Link $noBackLink -- objekt odkazu pokud neexistuje zpětný
    * @param int $maxLevel -- maximální level zpětného odkazu
    * @param int $curLevel -- (option) aktuální level (def: $maxLevel+1)
    * @param Url_Link $curLink -- (option) nový zpětný odkaz
    * @return Url_Link
    */
   public function back(Url_Link $noBackLink, $maxLevel, $curLevel = null, Url_Link $curLink = null) {
      if($curLink === null) $curLink = $this;
      if($curLevel === null) $curLevel = $maxLevel+1;
      // init pokud není
      if(!isset ($_SESSION['linkBack'][$this->category])) {
         $this->backInit($this->clear());
      }

      // vrázení odkazu
      for ($level = $maxLevel; $level >= 0; $level--) {
         if(isset ($_SESSION['linkBack'][$this->category][$level])) {
            $noBackLink = $_SESSION['linkBack'][$this->category][$level];
            break;
         }
      }
      // vytvoření aktuálního odkazu
      $this->setBack($curLink,$curLevel);
      return $noBackLink;
   }
}
//echo("PHP_SELF: ".$_SERVER["PHP_SELF"]."<br>");
//echo("SERVER_NAME: ".$_SERVER["SERVER_NAME"]."<br>");
//echo("QUERY_STRING: ".$_SERVER["QUERY_STRING"]."<br>");
//echo("DOCUMENT_ROOT: ".$_SERVER["DOCUMENT_ROOT"]."<br>");
//echo("SCRIPT_FILENAME: ".$_SERVER["SCRIPT_FILENAME"]."<br>");
//echo("SCRIPT_NAME: ".$_SERVER["SCRIPT_NAME"]."<br>");
//echo("REQUEST_URI: ".$_SERVER["REQUEST_URI"]."<br>");
//
//PHP_SELF: /skrznaskrz/admin/index.php
//SERVER_NAME: dev.vypecky.info
//QUERY_STRING: category=10
//DOCUMENT_ROOT: /var/www/dev/htdocs/
//SCRIPT_FILENAME: /var/www/dev/htdocs/skrznaskrz/admin/index.php
//SCRIPT_NAME: /skrznaskrz/admin/index.php
//REQUEST_URI: /skrznaskrz/admin/index.php?category=10
//
//PHP_SELF: /newwebvypecky/index.php
//SERVER_NAME: dev.vypecky.info
//QUERY_STRING: category=9&action=show
//DOCUMENT_ROOT: /var/www/dev/htdocs/
//SCRIPT_FILENAME: /var/www/dev/htdocs/newwebvypecky/index.php
//SCRIPT_NAME: /newwebvypecky/index.php
//REQUEST_URI: /newwebvypecky/index.php?category=9&action=show
//REQUEST_URI: /index.php?cat=2
//	+--http://sprava.vypecky.info/index.php?cat=2
//REQUEST_URI: /
//	+--http://sprava.vypecky.info/
?>