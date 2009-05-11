<?php
/**
 * Description of UrlRequest
 * Třída slouží pro parsování a obsluhu požadavků v URL adrese.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id$ VVE3.9.3 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract		Třída pro obsluhu a UrlReqestu
 */
class UrlRequest {
     /**
      * Název parametru s typem media
      */
   const PARAM_MEDIA_TYPE_PREFIX = 'media';

     /**
      * Typ media pro web
      */
   const MEDIA_TYPE_WWW	= 'www';

     /**
      * Typ media pro web
      */
   const MEDIA_TYPE_PRINT	= 'print';

     /**
      * Přenosový protokol
      */
   const TRANSFER_PROTOCOL = 'http://';

     /**
      * $GET proměná s název epluginu
      */
   const GET_EPLUGIN_NAME = 'eplugin';

   /**
    * Název Supported Services EPluginu
    */
   const SUPPORTSERVICES_EPLUGIN_NAME = 'eplugin';
     /**
      * $GET proměná s název jspluginu
      */
   const GET_JSPLUGIN_NAME = 'jsplugin';

   /**
    * Název Supported Services JsPluginu
    */
   const SUPPORTSERVICES_JSPLUGIN_NAME = 'jsplugin';

   /**
    * proměnná s typem sitemap
    */
   const SUPPORTSERVICES_SITEMAP_NAME = 'sitemap';

   /**
    * Název proměnné, kde je uložen název SupportedServices
    */
   const SUPPORTED_SERVICES_ARR_NAME = 'name';

   /**
    * Název proměné kde jsou uloženy ostatní parametry pro Supported Services
    * tj. co je za souborem
    */
   const SUPPORTED_SERVICES_ARR_PARAMS = 'name';

   /**
    * Oddělovač prametrů hlavní url
    */
   const URL_SEPARATOR = '/';

   /**
    * Název konstanty pro specialní stránku hledání
    */
   const SPECIAL_PAGE_SEARCH = 'search';

   /**
    * Název konstanty pro specialní stránku mapy webu
    */
   const SPECIAL_PAGE_SITEMAP = 'sitemap';

   /**
    * Obsahuje typ media
    * @var array
    */
   private static $media = 'www';

   /**
    * Obsahuje současnou url adresu
    * @var string
    */
   private static $currentUrl = null;

   /**
    * Základní URL adresa aplikace
    * @var string
    */
   private static $baseWebUrl = null;

   /**
    * Adresa serveru (např. www.seznam.cz)
    * @var string
    */
   private static $serverName = null;

   /**
    * Jméno scriptu
    * @var string
    */
   private static $scriptName = null;

   /**
    * Objekt akce modulu
    * @var Action
    */
   private $moduleAction = null;

   /**
    * Objekt cest modulu
    * @var Routes
    */
   private $moduleRoutes = null;

   /**
    * Proměná obsahuje url část pro media
    * @var string
    */
   private static $currentMediaUrlPart = null;

   /**
    * pole s regexy pro SupportedSeervices, podle nich se rozpoznávají
    * @var array
    */
   private static $notNormalUrlArrayRegex = array(self::SUPPORTSERVICES_SITEMAP_NAME => '^sitemap.(xml|txt)$',
      self::SUPPORTSERVICES_EPLUGIN_NAME => '^eplugin[^\.]*.js(.*)$',
      self::SUPPORTSERVICES_JSPLUGIN_NAME => '^jsplugin[^\.]*.js(.*)$');

   /**
    * Jestli je zvoleno SupportedServices
    * @var bool
    */
   private static $isSupportedServices = false;

   /**
    * Která ze Supported Services je volána
    * @var string
    */
   private static $supportedServicesType = null;

   /**
    * Název služby supproted services
    * @var string
    */
   private static $supporteServicesName = null;

   /**
    * Název souboru služby supproted services
    * @var string
    */
   private static $supporteServicesFile = null;

   /**
    * Parametry služby supproted services
    * @var string
    */
   private static $supporteServicesParams = null;

   /**
    * Regulerni vyrazy pro vyhodnocování specielních stránek (search, sitemap,
    * atd.), které jsou nezávislé na kategoriích
    * @var array
    */
   private static $specialPagesRegex = array(self::SPECIAL_PAGE_SEARCH => '\?search=([^&]*)&?p?a?g?e?=?([0-9]*)?',
      self::SPECIAL_PAGE_SITEMAP => 'sitemap.html');

   /**
    * Název specialní stránky
    * @var string
    */
   private static $specialPageName = null;

   /**
    * Jestli je zpracováván ajax soubor
    * @var boolean
    */
   private static $isAjax = false;

   /**
    * O jaký typ ajax se jedná (module, aplugin)
    * @var string
    */
   private static $ajaxType = null;

   /**
    * Název modulu nebo epluginu
    * @var string
    */
   private static $ajaxName = null;

   /**
    * Parametry ajax souboru
    * @var array
    */
   private static $ajaxParams = array();
   /**
    * Konstruktor
    */
   public function  __construct(Action $action, Routes $routes) {
      $this->moduleAction = $action;
      $this->moduleRoutes = $routes;
   }

     /**
      * Metoda inicializuje požadavky v URL
      */
   public static function factory() {
      //		Vytvoření url
      self::createUrl();
      //		Parsování url
      if(self::checkSpecialUrl()){
         self::parseSpecialUrl();
      } else if(self::checkAjaxFileUrl()){
         self::parseAjaxUrl();
      } else if(self::checkNormalUrl()){
         self::parseUrl();
      } else {
         self::parseUrlForsupportedServices();
      }
   }

   /**
    * Meto zjišťuje, zda se jedná o Normální URL nebo pro SupportedServices
    * (eplugin, jsplugin, sitemap atd.)
    * @return boolean true pokud se jedná o normální url
    */
   private static function checkNormalUrl() {
      $url = ereg_replace("^(.*)/$", "\\1", self::$currentUrl);
      // projdem adresu jestli se nejedná o SupportServices
      foreach (self::$notNormalUrlArrayRegex as $key => $regex) {
         if(eregi($regex, $url)){
            self::$supportedServicesType = $key;
            self::$isSupportedServices = true;
            return false;
         }
      }
      return true;
   }

   /**
    * Metoda zkontroluje jesli se nejedná o specialní URl obsažené v enginu
    */
   private static function checkSpecialUrl() {
      foreach (self::$specialPagesRegex as $regex) {
         if(ereg('^[a-z]{0,3}/?'.$regex.'$', self::$currentUrl)){
            return true;
         }
      }
      return false;
   }

   /**
    * Metoda kontroluje jestli se nejedná o url s ajax akcí
    */
   private static function checkAjaxFileUrl(){
      if(ereg('^([a-zA-Z]{2,3}/)?ajax(module|eplugin)([^.]+).php', self::$currentUrl)){
         self::$isAjax = true;
         return true;
      }
      return false;
   }

   /**
    * Metoda parsuje celou url do pole s jednotlivými proměnými
    */
   private static function parseUrl() {
      //		Rozdělíme řetězec podle separátorů
      $urlItems = $urlItemsBack = array();
      //		Odstranění posledního lomítka
      $url = ereg_replace("^(.*)/$", "\\1", self::$currentUrl);
      $urlItems = $urlItemsBack = explode(URL_SEPARATOR, $url);
      reset($urlItems); // reset pole aby bylo na začátku
      $isCategory = $lang = false;
      //		Kontrola jestli je zadán jazyk
      if(Links::checkLangUrlRequest(pos($urlItems))){
         $lang = true;
         unset ($urlItems[key($urlItems)]);
      }
      //		Kontrola jestli je zadána kategorie
      if(Links::checkCategoryUrlRequest(pos($urlItems))){
         $isCategory = true;
         unset ($urlItems[key($urlItems)]);
      }
      // zjištění, jestli je nějáká kategorie nebo se jedná o neexistující stránku
      if(!$lang AND !$isCategory AND $urlItemsBack[0] != null){
         AppCore::setErrorPage(); // zapnem Chybovou stránku
         return false;
      }
      //  Kontrola předání cesty pokud je definována
      if(Links::checkRouteUrlRequest(pos($urlItems))){
         unset ($urlItems[key($urlItems)]);
      }
      //		Načetní článku FORMAT: "nazev-id"
      if(Links::checkArticleUrlRequest(pos($urlItems))){
         unset ($urlItems[key($urlItems)]);
      }
      //		Načtení akce FORMAT: nazev_{action name (např. char)}-id_item
      if(Links::checkActionUrlRequest(pos($urlItems))){
         unset ($urlItems[key($urlItems)]);
      }
      //		Načtení typu media FORMAT: media{typ media např www,print}
      $matches = array();
      $expresion = '^'.self::PARAM_MEDIA_TYPE_PREFIX.'([a-zA-Z]+)$';
      if(eregi($expresion, pos($urlItems), $matches)){
         self::$media = $matches[1];
         self::$currentMediaUrlPart = pos($urlItems);
         unset ($urlItems[key($urlItems)]);
      }
      //        Vybrání ostatních  parametrů
      if(isset($urlItems) AND pos($urlItems) != null){
         Links::chackOtherUrlParams($urlItems);
      }
   }

   /**
    * Metoda parsuje url pro Supported Services (eplugins, jsplugins, sitemp, atd.)
    */
   private static function parseUrlForsupportedServices() {
      //		Odstranění posledního lomítka
      $url = ereg_replace("^(.*)/$", "\\1", self::$currentUrl);
      $name = $file = $params = null;
      $regexResult = array();
      switch (self::$supportedServicesType) {
         case self::SUPPORTSERVICES_EPLUGIN_NAME:
            ereg('^eplugin([^\./]*)/([^\.]*\.js)\?(.*)$', $url, $regexResult);
            $name = $regexResult[1];
            $file = $regexResult[2];
            $params = $regexResult[3];
            break;
         case self::SUPPORTSERVICES_JSPLUGIN_NAME:
            ereg('^jsplugin([^\./]*)/([^\.]*\.js)\?(.*)$', $url, $regexResult);
            $name = $regexResult[1];
            $file = $regexResult[2];
            if(isset ($regexResult[3])){
               $params = $regexResult[3];
            }
            break;
         case self::SUPPORTSERVICES_SITEMAP_NAME:
            ereg(self::$notNormalUrlArrayRegex[self::SUPPORTSERVICES_SITEMAP_NAME], $url, $regexResult);
            $name = $regexResult[1];
         default:
            break;
      }
      self::$supporteServicesName = $name;
      self::$supporteServicesFile = $file;
      self::$supporteServicesParams = $params;
   }

   /**
    * Metoda parsuje specialní url
    */
   private static function parseSpecialUrl() {
      $regexArr = array();
      //		Rozdělíme řetězec podle separátorů
      $urlItems = $urlItemsBack = array();
      //		Odstranění posledního lomítka
      $url = ereg_replace("^(.*)/$", "\\1", self::$currentUrl);
      $urlItems = $urlItemsBack = explode(URL_SEPARATOR, $url);
      reset($urlItems); // reset pole aby bylo na začátku
      // jazyk
      if(Links::checkLangUrlRequest(pos($urlItems))){
         $lang = true;
         unset ($urlItems[key($urlItems)]);
      }
      if(ereg(''.self::$specialPagesRegex[self::SPECIAL_PAGE_SEARCH].'$', $urlItems[key($urlItems)], $regexArr)){
         self::$specialPageName = self::SPECIAL_PAGE_SEARCH;
         Search::factory($regexArr[1], $regexArr[2]);
      } else if(ereg(''.self::$specialPagesRegex[self::SPECIAL_PAGE_SITEMAP].'$', $urlItems[key($urlItems)], $regexArr)){
         self::$specialPageName = self::SPECIAL_PAGE_SITEMAP;
      }
   }

   /**
    * Metoda parsuje url pro ajax akci
    */
   private static function parseAjaxUrl(){
      $regexResult = array();
      ereg('^([a-zA-Z]{2,3}/)?ajax(module|eplugin)([^.]+).php\??(.*)$', self::$currentUrl,$regexResult);
      self::$ajaxType = $regexResult[2];
      self::$ajaxName = $regexResult[3];
      if (isset ($regexResult[4])){
         self::$ajaxParams = $regexResult[4];
      }
      

   }

   /**
    * Metoda vytvoří url a uloží ji do $currentUrl
    */
   private static function createUrl() {
      $fullUrl = $_SERVER['REQUEST_URI'];
      self::$scriptName = $_SERVER["SCRIPT_NAME"];
      self::$serverName = $_SERVER["SERVER_NAME"];
      //		Najdeme co je cesta k aplikaci a co je předaná url
      self::$currentUrl = substr($fullUrl, strpos(self::$scriptName, AppCore::APP_MAIN_FILE));
      //		Vytvoříme základní URL cestu k aplikaci
      $positionLastChar=strrpos(self::$scriptName, self::URL_SEPARATOR);
      self::$baseWebUrl=self::TRANSFER_PROTOCOL.self::$serverName.substr(self::$scriptName, 0, $positionLastChar).self::URL_SEPARATOR;
   }


   /**
    * Metoda vrací základní URL cestu k aplikaci
    * @return string
    */
   public static function getBaseWebDir() {
      return self::$baseWebUrl;
   }

   /**
    * Metoda vrací typ média pro načtenou stránku
    * @return string
    */
   public static function getMediaType() {
      return self::$media;
   }

   /**
    * Metoda vrací URl část řetězce s nastaveným typem média
    * @return string -- url část
    */
   public static function getCurrentMediaUrlPart() {
      return self::$currentMediaUrlPart;
   }

   /**
    * Metoda zevolí typ kontroleru modulu
    */
   public function choseController() {
      //		Vyvoříme objekt článku kvůli zjišťování přítomnosti článku
      $article = new Article();
      //		pokud není akce
      if(!$this->moduleAction->isAction()){
         //			pokud je vybrán článek
         if($article->isArticle() AND !$this->moduleAction->isSomeAction()){
            $action = strtolower($this->moduleAction->getDefaultArticleAction());
         }
         //			Pokud není vybrán článek
         else {
            $action = strtolower(AppCore::MODULE_MAIN_CONTROLLER_PREFIX);
         }
      }
      //		Pokud je vybrána akce
      else {
         $action = $this->moduleAction->getSelectedAction();
      }
      //		Přiřazení routy
      if($this->moduleRoutes->isRoute()){
         $action = ucfirst($action);
         $action = $this->moduleRoutes->getRoute().$action;
      }
      return $action;
   }

   /**
    * Metoda vrací pokud je nastavena SupportedServices
    * @return bool true pokud se zpracovává
    */
   public static function isSupportedServices() {
      return self::$isSupportedServices;
   }

   /**
    * Metoda vrací typ SupportedServices (Eplugin, JsPlugin, ...)
    * @return string -- konstanta SUPPORTED_SERVICES_XXX
    */
   public static function getSupportedServicesType() {
      return self::$supportedServicesType;
   }

   /**
    * Metoda vrací název SupportedServices (Epluginu, JsPluginu, ...)
    * @return string -- název služby, záleží na typu (např. tinymce, ...)
    * u typu Sitemap vrací xml nebo txt
    */
   public static function getSupportedServicesName(){
      return self::$supporteServicesName;
   }

   /**
    * Metoda vrací název souboru SupportedServices (Epluginu, JsPluginu, ...)
    * @return string -- název souboru
    */
   public static function getSupportedServicesFile(){
      return self::$supporteServicesFile;
   }

   /**
    * Metoda vrací parametry SupportedServices, všechny co jsou za otazníkem
    * @return string -- string parametry, nerozparsované nebo null
    */
   public static function getSupportedServicesParams() {
      return self::$supporteServicesParams;
   }

   /**
    * Metoda vrací pokud je nastavena SpecialPage
    * @return bool true pokud se zpracovává
    */
   public static function isSpecialPage() {
      if(self::$specialPageName == null){
         return false;
      }
      return true;
   }

   /**
    * Metoda vrací název speciální stránky
    * @return string -- konstanta SPECIAL_PAGE_XXX
    */
   public static function getSpecialPage() {
      return self::$specialPageName;
   }

   /**
    * Metoda vrcí regulerní výraz pro specielní stránku
    * @param string $pageType -- typ specielní stránky constanta SPECIAL_PAGE_XXX
    * @return string -- regulerní výraz
    */
   public static function getSpecialPageRegexp($pageType) {
      return self::$specialPagesRegex[$pageType];
   }

   /**
    * Metoda vrací jestli je spuštěn ajax požadavek
    * @return boolean -- true pokud je ajax požadavek
    */
   public static function isAjaxRequest() {
      return self::$isAjax;
   }

   /**
    * Metoda vrací typ ajax požadavku - (module, eplugin)
    * @return string
    */
   public static function getAjaxType() {
      return self::$ajaxType;
   }

   /**
    * Metoda vrací název ajax modulu nebo epluginu
    * @return string -- název 
    */
   public static function getAjaxName() {
      return self::$ajaxName;
   }

   /**
    * Metoda vrací řetězec s parametry ajax souboru
    * @return string -- řetězec parametrů
    */
   public static function getAjaxFileParams() {
      return self::$ajaxParams;
   }
}
?>
