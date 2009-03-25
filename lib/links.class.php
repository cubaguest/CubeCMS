<?php
/**
 * Třída pro práci s odkazy.
 * Třída pro tvorbu a práci s odkazy aplikace, umožňuje jejich pohodlnou
 * tvorbu a validaci, popřípadě změnu jednotlivých parametrů. Umožňuje také
 * přímé přesměrování na zvolený (vytvořený) odkaz pomocí klauzule redirect.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.5.0 $Revision: $
 * @author			$Author: $ $Date:$
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro práci s odkazy
 */

class Links {
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
    * Oddělovatč mezi názvem článku a id článku
    * @var char
    */
   const URL_SEP_ARTICLE_ID = '-';
   /**
    *
    * Oddělovatč mezi názvem kategorie a id kateogire
    * @var char
    */
   const URL_SEP_CAT_ID = '-';

   /**
    * Oddělovač v url mezi popisem akce a typem akce
    * @var char
    */
   const URL_ACTION_LABEL_TYPE_SEP = '_';

   /**
    * Oddělovač url mezi typem akce a id item
    * @var char
    */
   const URL_ACTION_TYPE_ID_SEP = '-';

   /**
    * Názec souboru pro stahování dat
    * @var string
    */
   const DOWNLOAD_FILE = 'download.php';

   /**
    * Název parametru v url, přo který se přenáší název stahovanéh souboru
    * @var string
    */
   const DOWNLOAD_FILE_FILE_PARAM = 'file';

   /**
    * Název parametru v url, přo který se přenáší adresář stahovanéh souboru
    * @var string
    */
   const DOWNLOAD_FILE_DIR_PARAM = 'url';

   /**
    * Název položky pole se jménem
    * @var string
    */
   const LINK_ARRAY_ITEM_NAME = 'name';

   /**
    * Název položky pole s idčkem
    * @var string
    */
   const LINK_ARRAY_ITEM_ID = 'id';

   /**
    * Název položky pole s volitelným parametrem
    * @var string
    */
   const LINK_ARRAY_ITEM_OPTION = 'option';

   /**
    * Oddělovač předdefinované routy
    * @var char
    */
   const PRETEFINED_ROUTES_ID_PREFIX = 'p';

   /**
    * Proměná s typem přenosového protokolu
    * @var string
    */
   private static $user_transfer_protocol = null;

   /**
    * Proměnná s názvem jazyka
    * @var string
    */
   private static $currentlang = null;

   /**
    * Proměnná s názvem jazyka
    * @var string
    */
   private $lang = null;

   /**
    * Proměnná s názvem vybrané kategorie kategorie
    * @var array
    */
   private static $currentCategory = array();

   /**
    * Proměnná s názvem kategorie
    * @var array
    */
   private $category = array();

   /**
    * Proměná se zvolenou cestou
    * @var array
    */
   private static $currentRoute = array();

   /**
    * Proměná se zvolenou cestou
    * @var array
    */
   private $route = array();

   /**
    * Proměná s názvem článku (article key)
    * @var array
    */
   private static $currentArticle = array();

   /**
    * Proměná s názvem článku (article key)
    * @var array
    */
   private $article = array();

   /**
    * Proměná s názvem akce (action)
    * @var array
    */
   private static $currentAction = array();

   /**
    * Proměná s názvem akce (action)
    * @var array
    */
   private $action = array();

   /**
    * Proměná s názvem média
    * @var string
    */
   private $mediaType = null;

   /**
    * Proměná s názvem souboru
    * @var string
    */
   private $file = null;

   /**
    * Jestli se má tvořit nový odkaz k rootu webu
    * @var boolean
    */
   private $onlyWebRoot = false;

   /**
    * Pole s parsovatelnými parametry v url
    * @var array
    */
   private static $currentParamsArray = array();

   /**
    * Pole s parsovatelnými parametry v url
    * @var array
    */
   private $paramsArray = array();

   /**
    * Pole s ostatními parametry v url
    * @var array
    */
   private static $currentParamsNormalArray = array();

   /**
    * Pole s ostatními parametry v url
    * @var array
    */
   private $paramsNormalArray = array();

   /**
    * Konstruktor nastaví základní adresy a přenosový protokol
    *
    * @param boolean -- (option)true pokud má být vrácen čistý link jenom s kategorií(pokud je vybrána) a jazykem
    * @param boolean -- (option)true pokud má být vráce naprosto čistý link (web root)
    */
   function __construct($clear = false, $onlyWebRoot = false) {
      $this->clearLink = $clear;
      $this->onlyWebRoot = $onlyWebRoot;
      $this->_init();
   }

   /*
    * STATICKÉ METODY
    */

   /**
    * metoda nastavuje transportní protokol
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
      if(self::$user_transfer_protocol == null){
         return UrlRequest::TRANSFER_PROTOCOL;
      } else {
         return self::$user_transfer_protocol;
      }
   }

   /**
    * Metoda vrací adresu k web aplikaci
    * @return string -- adresa ke kořenu aplikace
    */
  	public static function getMainWebDir() {
      return UrlRequest::getBaseWebDir();
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
   public function category($catName = null, $catId = null){
      if($catName != null AND $catId != null){
         $help = new TextHelper();
         $this->category[self::LINK_ARRAY_ITEM_NAME] = rawurlencode($help->utf2ascii($catName));
         $this->category[self::LINK_ARRAY_ITEM_ID] = $catId;
      } else {
         $this->category[self::LINK_ARRAY_ITEM_NAME] = null;
         $this->category[self::LINK_ARRAY_ITEM_ID] = null;
      }
      return $this;
   }

   /**
    * Metoda nastavuje název a id routy
    * @param string -- název cesty
    *
    * @return Links -- objket Links
    */
   public function route($name = null, $id = null){
      //		Pokud je předána předdefinovaná cesta
      if(is_array($name)){
         $this->route[self::LINK_ARRAY_ITEM_NAME] = $name[0];
         $this->route[self::LINK_ARRAY_ITEM_ID] = $name[1];
         $this->route[self::LINK_ARRAY_ITEM_OPTION] = true;
      }
      //        pokud je předána uživatelská cesta
      if($name != null AND $id != null){
         $this->route[self::LINK_ARRAY_ITEM_NAME] = $name;
         $this->route[self::LINK_ARRAY_ITEM_ID] = $id;
         $this->route[self::LINK_ARRAY_ITEM_OPTION] = false;
      }
      return $this;
   }

   /**
    * Metoda nastavuje název article
    * @param string -- jméno článku
    * @param integer -- id článku
    *
    * @return Links -- objket Links
    */
   public function article($article = null, $idArticle = null){
      if($article != null AND $idArticle != null AND is_numeric($idArticle)){
         $this->article[self::LINK_ARRAY_ITEM_ID] = $idArticle;
         $article = htmlspecialchars_decode($article);
         $help = new TextHelper();
         $article = $help->utf2ascii($article);
         $article = urlencode($article);
         $this->article[self::LINK_ARRAY_ITEM_NAME] = $article;
      } else {
         $this->article[self::LINK_ARRAY_ITEM_ID] = null;
         $this->article[self::LINK_ARRAY_ITEM_NAME] = null;
      }
      return $this;
   }

   /**
    * Metoda nastavuje typ media
    * @param string -- jméno media
    *
    * @return Links -- objket Links
    */
   public function media($media = null){
      $this->mediaType = $media;
      return $this;
   }

   /**
    * Metoda nastavuje parametr nazev epluginu
    * @param string -- jméno epluginu
    *
    * @return Links -- objket Links
    */
   public function file($file = null){
      $this->file = $file;
      return $this;
   }

   /**
    * Metoda nastavuje název action
    * @param string -- jméno akce (action např. edit, show atd.)
    *
    * @return Links -- objket Links
    */
   public function action($action = null){
      if(!empty ($action)){
         $this->action[self::LINK_ARRAY_ITEM_NAME] = $action[0];
         $this->action[self::LINK_ARRAY_ITEM_OPTION] = $action[1];
         $this->action[self::LINK_ARRAY_ITEM_ID] = $action[2];
      } else {
         $this->action= null;
      }
      return $this;
   }

   /**
    * Metoda nastavuje název lokalizace
    * @param string -- jméno jazyka (action např. cs, en, de atd.)
    *
    * @return Links -- objket Links
    */
   public function lang($lang = null){
      $this->lang = $lang;
      return $this;
   }

   /**
    * Metoda přidá nebo změní daný parametr v URL
    * @param mixed $name -- objekt UrlParam nebo string
    * @param mixed $value -- (option) hodnota parametru, jen v případě přímého
    * předávání názvu parametru
    */
   public function param($name, $value = null) {
      //        Je vkládán objekt parametru
      if($name instanceof UrlParam){
         //            $name = new UrlParam();
         // Pokud se nejedná o normálový
         if(!$name->isNormalParam()){
            $add = true;
            // Projdem pole a otestujem hodnoty naproti regulérnímu výrazu parametru
            foreach ($this->paramsArray as $paramKey => $param) {
               if(ereg($name->getPattern(), $param)){
                  $add = false;
                  $this->paramsArray[$paramKey] = rawurlencode($name);
               }
            }

            //                Pokud parametr v url není
            if($add){
               array_push($this->paramsArray, rawurlencode($name));
            }
         } else {
            $this->paramsNormalArray[$name] = rawurlencode($value);
         }
      }
      //        Jedná se o běžně zadaný parametr
      else {
         if(!is_array($name)){
            $this->paramsNormalArray[$name] = rawurlencode($value);
         } else {
            foreach ($name as $key => $val) {
               $this->paramsNormalArray[$key] = rawurlencode($val);
            }
         }
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
      //        Je vkládán objekt parametru
      if($name instanceof UrlParam){
         //            $name = new UrlParam();
         // Pokud se nejedná o normálový
         if(!$name->isNormalParam()){
            // Projdem pole a otestujem hodnoty naproti regulérnímu výrazu parametru
            foreach ($this->paramsArray as $paramKey => $param) {
               if(ereg($name->getPattern(), $param)){
                  unset ($this->paramsArray[$paramKey]);
               }
            }
         } else {
            unset ($this->paramsNormalArray[$name]);
         }
      }
      //        Jedná se o běžně zadaný parametr
      else if($name != null) {
         unset($this->paramsNormalArray[$name]);
      }
      //        Odstranění všch parametrů (normálových i obyčejných)
      else {
         $this->paramsArray = array();
         $this->paramsNormalArray = array();
      }
      return $this;
   }

   /**
    * Metoda nastavuje znovunahrání stránky
    * @param string -- externí odkaz na který se má přesměrovat (option)
    */
   public function reload($link = null){
      if($link == null){
         header("location: ".$this);
      } else {
         header("location: ".(string)$link);
      }
      exit();
   }

   /*
    * PRIVÁTNÍ METODY
    */

   /**
    * Metoda inicializuje odkazy
    *
    */
   private function _init(){
      if(!$this->onlyWebRoot){
         $this->lang = self::$currentlang;
         //            $this->lang = Locale::getLang();
         $this->category = self::$currentCategory;
         if(!$this->clearLink){
            $this->route = self::$currentRoute;
            $this->article = self::$currentArticle;
            $this->action = self::$currentAction;
            $this->paramsArray = self::$currentParamsArray;
            $this->paramsNormalArray = self::$currentParamsNormalArray;
            $this->mediaType = UrlRequest::getCurrentMediaUrlPart();
         }
      }
   }

   /**
    * Metoda složí dohromady parametry pro url
    */
   private function completeUrlParams(){
      return http_build_query($this->paramsNormalArray);
   }

   /**
    * Metoda doplní část s názvem souboru
    */
   private function getFile() {
      if($this->file != null){
         return $this->file;
      } else {
         return null;
      }
   }

   /*
    * MAGICKÉ METODY
    */

   /**
     * Metoda převede objekt na řetězec
     *
     * @return string -- objekt jako řetězec
     */
   public function __toString(){
      $returnString = UrlRequest::getBaseWebDir();
      if($this->lang != null){
         $returnString.=$this->getLang();
      }
      if($this->getCategory() != null){
         $returnString.=$this->getCategory();
      }
      if($this->getRoute() != null){
         $returnString.=$this->getRoute();
      }
      if($this->getArticle() != null){
         $returnString.=$this->getArticle();
      }
      if($this->getAction() != null){
         $returnString.=$this->getAction();
      }
      //        Parsovatelné parametry
      if($this->getParams() != null){
         $returnString.=$this->getParams();
      }
      /**
       * @todo What is this? Know anybody what is this doing?
       */
      if($this->getFile() != null){
         $returnString.=$this->getFile();
      }
      //        normálové parametry
      if($this->getNormalParams() != null){
         $returnString.=$this->getNormalParams();
      }
      return $returnString;
   }

   /**
    * Metoda vrací link pro stažení souboru pomocí specílního dwsouboru
    * @param string -- cesta ks ouboru
    * @param string -- název souboru
    */
   public function getLinkToDownloadFile($dir, $file) {
      $dwLink = UrlRequest::getBaseWebDir().self::DOWNLOAD_FILE.self::URL_SEPARATOR_LINK_PARAMS.
      self::DOWNLOAD_FILE_DIR_PARAM.self::URL_SEP_PARAM_VALUE.urlencode($dir).
      self::URL_PARAMETRES_SEPARATOR.self::DOWNLOAD_FILE_FILE_PARAM.self::URL_SEP_PARAM_VALUE.
      urlencode($file);
      return $dwLink;
   }

   /**
    * Metoda vrací link pro stažení souboru pomocí specílního dwsouboru
    * @param string -- cesta ks ouboru
    * @param string -- název souboru
    */
   public static function getLinkToDwFile($dir, $file) {
      $dwLink = UrlRequest::getBaseWebDir().self::DOWNLOAD_FILE.self::URL_SEPARATOR_LINK_PARAMS.
      self::DOWNLOAD_FILE_DIR_PARAM.self::URL_SEP_PARAM_VALUE.urlencode($dir).
      self::URL_PARAMETRES_SEPARATOR.self::DOWNLOAD_FILE_FILE_PARAM.self::URL_SEP_PARAM_VALUE.
      urlencode($file);

      return $dwLink;
   }

   /*
    * Metody pro zpracování a tvorbu obsahu URL
    */

   /**
    * Metoda kontroluje, jestli se jedná o nastaveni jazykové mutace
    * FORMAT: en, cs, de
    *
    * @param string $lang -- řetězec s jazykem
    */
   public static function checkLangUrlRequest($lang) {
      $matches = array();
      if(eregi('^([a-zA-Z]{2})$', $lang, $matches)){
         Locale::setLang($matches[1]);
         self::$currentlang = $matches[1];
         return true;
      }
      return false;
   }

   /**
    * Metoda kontroluje, jestli se jedná o kategorii zadanou v url
    * FORMAT: nazev-id
    *
    * @param string $category -- řetězec s kategorií
    */
   public static function checkCategoryUrlRequest($category) {
      $matches = array();
      if(eregi('^([a-zA-Z0-9%\-]+)-([0-9]+)$', $category, $matches)){
         Category::setCurrentCategoryId($matches[2]);
         self::$currentCategory[self::LINK_ARRAY_ITEM_ID] = $matches[2];
         self::$currentCategory[self::LINK_ARRAY_ITEM_NAME] = $matches[1];
         return true;
      }
      return false;
   }

   /**
    * Metoda vrací kategorii
    * @return string -- název kategorie (klíč)
    */
   private function getCategory() {
      if(!empty ($this->category)){
         $categoryName = $this->category[self::LINK_ARRAY_ITEM_NAME];
         $utf = new TextHelper();
         $categoryName = $utf->utf2ascii($categoryName);
         return $categoryName.self::URL_SEP_CAT_ID
         .$this->category[self::LINK_ARRAY_ITEM_ID].UrlRequest::URL_SEPARATOR;
      }
      return null;
   }

   /**
    * Metoda kontroluje, jestli se jedná o routu zadanou v url
    * FORMAT: FORMAT: nazev-rid nebo nazev-r{char}id
    *
    * @param string $route -- řetězec s cestou
    */
   public static function checkRouteUrlRequest($route) {
      $matches = array();
      if(eregi('^([a-zA-Z0-9%\-]+)'.Routes::ROUTE_URL_ID_SEPARATOR.'r([a-z]?[0-9]+)$',$route, $matches)){

         self::$currentRoute[self::LINK_ARRAY_ITEM_NAME] = $matches[1];
         $matches2 = array();
         $pattern = '^r'.self::PRETEFINED_ROUTES_ID_PREFIX.'([0-9]+)';
         //		Pokud je předdefinovaná cesta
         if(eregi($pattern, $id, $matches2)){
            self::$currentRoute[self::LINK_ARRAY_ITEM_ID] = $matches2[1];
            self::$currentRoute[self::LINK_ARRAY_ITEM_OPTION] = true;
            Routes::setCurrentRouteId($matches2[2], true);
         }
         //		je použita výchozí cesta
         else if(eregi('r([0-9]+)', $id, $matches2)) {
            self::$currentRoute[self::LINK_ARRAY_ITEM_ID] = $matches2[1];
            self::$currentRoute[self::LINK_ARRAY_ITEM_OPTION] = false;
            Routes::setCurrentRouteId($matches2[2], false);
         }
         return true;
      }
      return false;
   }

   /**
    * Metoda vrací url část pro cestu
    * @return string -- url část
    */
   private function getRoute() {
      if(!empty ($this->route)){
         $utf = new TextHelper();
         $name = $utf->utf2ascii($this->route[self::LINK_ARRAY_ITEM_NAME]);
         $return = $name.self::URL_SEP_CAT_ID.'r';
         //            Pokud je předdefinovaná routa
         if($this->route[self::LINK_ARRAY_ITEM_OPTION]){
            $return .= self::PRETEFINED_ROUTES_ID_PREFIX;
         }
         return 	$return.$this->route[self::LINK_ARRAY_ITEM_ID].UrlRequest::URL_SEPARATOR;
      }
      return null;
   }

   /**
    * Metoda kontroluje, jestli se jedná o článek v URL
    * FORMAT: nazev-id
    *
    * @param string $article -- řetězec s článkem
    */
   public static function checkArticleUrlRequest($article) {
      $matches = array();
      if(eregi('^([a-zA-Z0-9%\-]+)-([0-9]+)$', $article, $matches)){
         Article::setCurrentArticleId($matches[2]);
         self::$currentArticle[self::LINK_ARRAY_ITEM_ID] = $matches[2];
         self::$currentArticle[self::LINK_ARRAY_ITEM_NAME] = $matches[1];
         return true;
      }
      return false;
   }

   /**
    * Metoda vrací část článek (article) pro url
    * @param string -- článek (article)
    */
   private function getArticle() {
      if(!empty ($this->article[self::LINK_ARRAY_ITEM_ID]) AND
         !empty ($this->article[self::LINK_ARRAY_ITEM_NAME])){
         $utf = new TextHelper();
         $name = $utf->utf2ascii($this->article[self::LINK_ARRAY_ITEM_NAME]);

         return rawurlencode($name).self::URL_SEP_ARTICLE_ID
         .$this->article[self::LINK_ARRAY_ITEM_ID].UrlRequest::URL_SEPARATOR;
      } else {
         return null;
      }
   }

   /**
    * Metoda kontroluje, jestli se jedná o akci v URL
    * FORMAT: nazev-id
    *
    * @param string $action -- řetězec s akcí
    */
   public static function checkActionUrlRequest($action) {
      $matches = array();
      $regex = '^([a-zA-Z0-9%\-]+)'.self::URL_ACTION_LABEL_TYPE_SEP.'([a-zA-Z]+)'
      .self::URL_ACTION_TYPE_ID_SEP.'([0-9]+)$';
      if(eregi($regex, $action, $matches)){
         Action::setAction($matches[2], $matches[3]);
         self::$currentAction[self::LINK_ARRAY_ITEM_ID] = $matches[3];
         self::$currentAction[self::LINK_ARRAY_ITEM_OPTION] = $matches[2];
         self::$currentAction[self::LINK_ARRAY_ITEM_NAME] = $matches[1];
         return true;
      }
      return false;
   }

   /**
    * Metoda vrací část akce (action) pro url
    * @param string -- akce (action)
    */
   private function getAction() {
      if(!empty ($this->action)){
         $utf = new TextHelper();
         $name = $utf->utf2ascii($this->action[self::LINK_ARRAY_ITEM_NAME]);

         return $name.self::URL_ACTION_LABEL_TYPE_SEP
         .$this->action[self::LINK_ARRAY_ITEM_OPTION]
         .self::URL_ACTION_TYPE_ID_SEP.$this->action[self::LINK_ARRAY_ITEM_ID]
         .UrlRequest::URL_SEPARATOR;
      } else {
         return null;
      }
   }

   /**
    * Metoda rozparsuje ostatní parametry v URL
    *
    * @param array -- pole s ostatními parametry v URL
    */
   public static function chackOtherUrlParams($params) {
      if(!empty ($params)){
         //  Vytažení ostatních parametrů v url do objektu UrrlParam
         foreach ($params as $item) {
            // Pokud se jedná o parametr přenášený pomocí objektu
            if($item[0] != '?'){
               array_push(self::$currentParamsArray, rawurldecode($item));
               UrlParam::setParam($item);
            }
            // Pokud  se jedná o normálový parametr
            else {
               // Budou parsovány
               self::$currentParamsNormalArray = self::parseNormalParams($item);
               UrlParam::setNormalParams(self::$currentParamsNormalArray);
            }
         }
      }
   }

   /**
    * Metoda parsuje normálové parametry a vrací je jako pole, kde klíč je název
    * a hodnota parametru je hodnota
    * @param string $params -- řetězec s parametry
    * @return array -- pole s parametry
    */
   private static function parseNormalParams($params) {
      $paramsArr= array();
      // odstrannění otazníku na začátku
      if($params[0] == '?'){
         $params = substr($params, 1, strlen($params)-1);
      }

      if($params != null){
         $tmpParamsArray = array();
         $tmpParamsArray = explode('&', $params);
         // projití všech parametrů
         foreach ($tmpParamsArray as $fullParam){
            $tmpParam = explode('=', $fullParam);
            // kontrola, jestli je parametr zadán správně
            if(isset($tmpParam[0]) AND isset($tmpParam[1])){
               $paramsArr[$tmpParam[0]] = urldecode($tmpParam[1]);
            }
         }
      }
      return $paramsArr;
   }

   /**
    * Metoda vrací část s parametry pro url (parsovatelné)
    * @param string -- řetězec s parametry
    */
   private function getParams() {
      $return = null;
      if(!empty ($this->paramsArray)){
         foreach ($this->paramsArray as $param) {
            $return .= rawurlencode($param).UrlRequest::URL_SEPARATOR;
         }
      }
      return $return;
   }

   /**
    * Metoda vrací část s parametry pro url (normálové)
    * @param string -- řetězec s parametry
    */
   private function getNormalParams() {
      $return = null;
      if(!empty ($this->paramsNormalArray)){
         $return = self::URL_SEPARATOR_LINK_PARAMS.$this->completeUrlParams($this->paramsNormalArray);
      }
      return $return;
   }

   /**
    * Metoda vrací část lang pro url
    * @param string -- lang
    */
   private function getLang() {
      if($this->lang != null){
         return $this->lang.UrlRequest::URL_SEPARATOR;
      } else {
         return null;
      }
   }

   /**
    * Metoda doplní část media
    * @todo patří vložit asij někam jinam, dořešit
    */
   private function getMedia() {
      if($this->mediaType != null){
         //			$this->param(self::GET_MEDIA, $this->mediaType);
      }
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