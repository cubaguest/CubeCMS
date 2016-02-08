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

class Url_Link extends Url {
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
    * Pole s pparametry v aktuální URL
    * @var array
    */
   protected static $currentParams = array();

   /**
    * Soubor který se má zobrazit
    * @var string
    */
   protected static $currentFile = null;

   /**
    * Kotva který se má zobrazit
    * @var string
    */
   protected static $currentAnchor = null;

   /**
    * Konstruktor nastaví základní adresy a přenosový protokol
    * @param boolean $clear -- (option) true pokud má být vrácen čistý link jenom s kategorií(pokud je vybrána) a jazykem
    * @param boolean $onlyWebRoot -- (option) true pokud má být vráce naprosto čistý link (web root)
    */
   function __construct($clear = false) {
      //      $this->onlyWebRoot = $onlyWebRoot;
      parent::__construct();
      $this->_init();
      if($clear) {
         $this->clear();
      }
   }

   /**
    * Metoda vrací adresu k web aplikaci
    * @return string -- adresa ke kořenu aplikace
    */
   public static function getMainWebDir() {
      return Url_Request::getBaseWebDir();
   }

   /**
    * Metoda vrací adresu k web aplikaci
    * @return string -- adresa ke kořenu aplikace
    */
   public static function getWebURL() {
      return Url_Request::getBaseWebDir();
   }
   
    /**
    * Metoda vrací základní odkaz na web (včetně jazyka)
    * @return Url_Link -- adresa aplikace
    */
   public static function getBaseLink() {
      $l = new Url_Link(true);
      $l->clear(true);
      if(Locales::getLang() != Locales::getDefaultLang()){
         $l->lang(Locales::getLang());
      }
      return $l;
   }

   /*
    * VEŘEJNÉ METODY
    */

   /**
    * Metoda nastavuje název a id kategorie
    * @param string -- klíč kategorie
    *
    * @return Url_Link -- objket Url_Link
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
    * Metoda nastavuje typ media
    * @param string -- jméno media
    *
    * @return Url_Link -- objket Url_Link
    */
   public function media($media = null) {
      $this->mediaType = $media;
      return $this;
   }

   /**
    * Metoda nastavuje název lokalizace
    * @param string -- jméno jazyka (action např. cs, en, de atd.)
    *
    * @return Url_Link -- objket Url_Link
    */
   public function lang($lang = null) {
      $this->lang = $lang;
      return $this;
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
    * Metoda odstraní všechny parametry v odkazu
    * @return Url_Link -- sám sebe
    */
   public function clear($withOutCategory = false) {
      $this->rmParam();
      $this->file(null);
      $this->route = null;
      $this->anchor = null;
      if($withOutCategory) {
         $this->category();
      }
      return $this;
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
      foreach (AppCore::$runVars as $var) {
         if(isset($_GET[$var])){
            $this->param($var, $_GET[$var]);
         }
      }
      
      $returnString = $this->getBaseUrl().'/';
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
      if($this->anchor != null) {
         $returnString.=$this->getAnchor();
      }
      $returnString = $this->repairUrl($returnString);
      return $returnString;
   }


   /**
    * Vnitřní metody
    */

   /**
    * Metoda vrací část lang pro url
    * @param string -- lang
    */
   protected function getLang() {
      if($this->lang != null) {
         return $this->lang.'/';
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
            return $this->category.'/';
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
    * Metoda inicializuje odkazy
    *
    */
   protected function _init() {
      $this->lang = self::$currentlang;
      $this->category = self::$currentCategory;
      $this->route = self::$currentRoute;
      $this->file = self::$currentFile;
      $this->paramsArray = self::$currentParams;
      $this->anchor = self::$currentAnchor;
   }


   /**
    * Deprecated
    */

   /**
    * Metoda inicializuje zpětný odkaz, kde bude tato metoda volána je považován za kořenový kontroller
    * @todo Tohle předělat !!!
    * @deprecated
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
    * @deprecated
    */
   private function setBack(Url_Link $link, $appLevel = 0) {
      $_SESSION['linkBack'][$this->category][$appLevel] = (string)$link;
      ksort($_SESSION['linkBack'][$this->category]);
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
         return 'http';
      } else {
         return self::$user_transfer_protocol;
      }
   }

   /**
    * Metoda vytvoří objekt odkazu aplikace na Admin kategorii podle ID
    * @return Url_Link_Module
    */
   public static function getCategoryAdminLink($idc){
      $link = new Url_Link_Module(true);
      $cat = Model_CategoryAdm::getCategoryByID($idc);
      if(!$cat){
         return new Url_Link(true);
      }
      $link->setModuleRoutes(Routes::createModuleRoutes(new Category_Admin(null, false, $cat)));
      return $link->clear(true)->category( is_object($cat) ? $cat->{Model_Category::COLUMN_URLKEY} : null);
   }

   /**
    * Metoda vytvoří objekt odkazu aplikace na kategorii podle ID
    * @return Url_Link_Module
    */
   public static function getCategoryLink($idc){
      $link = new Url_Link_Module(true);
      $cat = Category_Structure::getStructure(Category_Structure::ALL)->getCategory($idc);
      if(!$cat){
         return new Url_Link(true);
      }
      $link->setModuleRoutes(Routes::createModuleRoutes($cat->getCatObj()));
      return $link->clear(true)->category( is_object($cat) ? $cat->getCatObj()->getUrlKey() : null);
   }

   /**
    * Metoda vytvoří objekt odkazu aplikace na Admin kategorii podle modulu
    * @return Url_Link_Module
    */
   public static function getCategoryAdminLinkByModule($module){
      $link = new Url_Link_Module(true);
      $cat = Model_CategoryAdm::getCategoryByModule($module);
      if(!$cat){
         return new Url_Link(true);
      }
      $link->setModuleRoutes(Routes::createModuleRoutes(new Category_Admin(null, false, $cat)));
      return $link->clear(true)->category(is_object($cat) ? $cat->{Model_Category::COLUMN_URLKEY} : null);

   }

   /**
    * Metoda vytvoří pole objektů s odkazy aplikace na kategorie podle modulu
    * @return array of Url_Link_Module
    */
   public static function getCategoryLinkByModule($module){
      /*
       * jednodušší je udělat sql dotaz na kategorie
       */
      $cats = Model_Category::getCategoryListByModule($module);
      $linksArr = array();
      foreach($cats as $cat) {
         $link = new Url_Link_Module(true);
         $link->setModuleRoutes(Routes::createModuleRoutes(new Category(null, false, $cat)));
         $linksArr[$cat->{Model_Category::COLUMN_ID}] = $link->clear(true)->category((string)$cat->{Model_Category::COLUMN_URLKEY});
      }
      return $linksArr;
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