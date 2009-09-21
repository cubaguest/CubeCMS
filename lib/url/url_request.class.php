<?php
/**
 * Description of Dispatcher
 * Třída slouží pro parsování a obsluhu základních požadavků v URL adrese.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id$ VVE 6.0 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract		Třída pro obsluhu Url adres
 */
class Url_Request {
/**
 * Oddělovač částí URL adresy
 */
   const URL_SEPARATOR = '/';

   const URL_TYPE_NORMAL = 'normal';
   const URL_TYPE_ENGINE_PAGE = 'special';
   const URL_TYPE_MODULE = 'module';
   const URL_TYPE_COMPONENT = 'component';
   const URL_TYPE_JSPLUGIN = 'jsplugin';


   /**
    * jestli se jedná o normální url nebo url s podpůrnými službami
    * @var string
    */
   private $urlType = self::URL_TYPE_NORMAL;

   /**
    * Ćást URL s cestami modulu
    * @var string
    */
   private $moduleUrlPart = null;

   /**
    * Přenosový protokol
    * @var string
    */
   private static $transferProtocol = 'http://';

   /**
    * Obsahuje současnou url adresu
    * @var string
    */
   private static $fullUrl = null;

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
    * Název kategorie požadavku
    * @var string
    */
   private $category = null;

   /**
    * Název modulu, epluginu nebo jspluginu požadavku
    * @var string
    */
   private $name = null;

   /**
    * Název akce požadavku
    * @var string
    */
   private $action = null;

   /**
    * Ostatní parametry v URL
    * @var string
    */
   private $parmas = null;

   /**
    * Parametr jazyka v URL
    * @var string
    */
   private $lang = null;

   /**
    * typ výstupu aplikace (default je html)
    * @var string
    */
   private $outputType = 'html';

   /**
    * pole s regexy pro SupportedSeervices, podle nich se rozpoznávají
    * @var array
    */
   //   private static $notNormalUrlArrayRegex = array(self::SUPPORTSERVICES_SITEMAP_NAME => '^sitemap.(xml|txt)$',
   //   self::SUPPORTSERVICES_EPLUGIN_NAME => '^eplugin[^\.]*.js(.*)$',
   //   self::SUPPORTSERVICES_JSPLUGIN_NAME => '^jsplugin[^\.]*.js(.*)$');

   /**
    * Jestli je zvoleno SupportedServices
    * @var bool
    */
   //   private static $isSupportedServices = false;

   /**
    * Která ze Supported Services je volána
    * @var string
    */
   //   private static $supportedServicesType = null;

   /**
    * Název služby supproted services
    * @var string
    */
   //   private static $supporteServicesName = null;

   /**
    * Název souboru služby supproted services
    * @var string
    */
   //   private static $supporteServicesFile = null;

   /**
    * Parametry služby supproted services
    * @var string
    */
   //   private static $supporteServicesParams = null;

   /**
    * Regulerni vyrazy pro vyhodnocování specielních stránek (search, sitemap,
    * atd.), které jsou nezávislé na kategoriích
    * @var array
    */
   //   private static $specialPagesRegex = array(self::SPECIAL_PAGE_SEARCH => '\?search=([^&]*)&?p?a?g?e?=?([0-9]*)?',
   //   self::SPECIAL_PAGE_SITEMAP => 'sitemap.html');

   /**
    * Název specialní stránky
    * @var string
    */
   //   private static $specialPageName = null;

   /**
    * Jestli je zpracováván ajax soubor
    * @var boolean
    */
   //   private static $isAjax = false;

   /**
    * Konstruktor
    */
   public function  __construct() {
      $this->checkUrlType();
      // pokud je normální url vytvoříme část pro modul
//      if($this->urlType == self::URL_TYPE_NORMAL) {
//
//      }
   }

   /**
    * Metoda inicializuje požadavky v URL
    */
   public static function factory() {
   //		Vytvoření url
      $fullUrl = $_SERVER['REQUEST_URI'];
      self::$scriptName = $_SERVER["SCRIPT_NAME"];
      self::$serverName = $_SERVER["SERVER_NAME"];
      //		Najdeme co je cesta k aplikaci a co je předaná url
      self::$fullUrl = substr($fullUrl, strpos(self::$scriptName, AppCore::APP_MAIN_FILE));
      //		Vytvoříme základní URL cestu k aplikaci
      $positionLastChar=strrpos(self::$scriptName, self::URL_SEPARATOR);
      self::$baseWebUrl=self::$transferProtocol.self::$serverName
          .substr(self::$scriptName, 0, $positionLastChar).self::URL_SEPARATOR;;
   }

   /**
    * Metoda vrací typ url adresy (normal, eplugin, module, jsplugin, atd)
    * @return string -- typ url adresy
    */
   public function getUrlType() {
      return $this->urlType;
   }

   /**
    * Metoda zjistí typ url
    */
   public function checkUrlType() {
   // jesli se zpracovává soubor modulu
      if($this->parseSpecialPageUrl()) {
         $this->urlType = self::URL_TYPE_ENGINE_PAGE;
      } else if($this->parseModuleUrl()) {
         $this->urlType = self::URL_TYPE_MODULE;
      } else if($this->parseComponentUrl()) {
         $this->urlType = self::URL_TYPE_COMPONENT;
      } else if($this->parseJsPluginUrl()) {
         $this->urlType = self::URL_TYPE_JSPLUGIN;
      } else {
         $this->parseNormalUrl();
         $this->urlType = self::URL_TYPE_NORMAL;
      }
      // doplněnívýchozích hodnot do objektů odkazů
      // nastavení odkazů
      Url_Link::setCategory($this->getCategory());
      Url_Link_Module::setRoute($this->getModuleUrlPart());
      Url_Link::setParams($this->getUrlParams());
      Url_Link::setLang($this->getUrlLang());
      Locale::setLang($this->getUrlLang());
   }

   /**
    * Meto zjišťuje, zda se jedná o Normální URL nebo pro SupportedServices
    * (eplugin, jsplugin, sitemap atd.)
    * @return boolean true pokud se jedná o normální url
    */
   private function parseNormalUrl() {
      $matches = array();
      if(!preg_match("/(?:(?P<lang>[a-z]{2})\/)?(?P<category>[a-zA-Z0-9_-]*)\/(?P<other>[^?\/]*)\/?\??(?P<params>.*)/i", self::$fullUrl, $matches)) {
         return false;
      }
//      var_dump($matches);
      $this->category = $matches['category'];
      $this->moduleUrlPart = $matches['other'];
      $this->parmas = $matches['params'];
      $this->lang = $matches['lang'];
      return true;
   }

   /**
    * Metoda zkontroluje jesli se nejedná o specialní stránky obsažené v enginu
    * (hledání, sitemap, atd)
    */
   private function parseSpecialPageUrl() {
   //      foreach (self::$specialPagesRegex as $regex) {
   //         if(ereg('^[a-z]{0,3}/?'.$regex.'$', self::$currentUrl)) {
   //            return true;
   //         }
   //      }
      return false;
   }

   /**
    * Metoda zkontroluje jesli se nejedná o url k akci modulu (např. při ajax requestu)
    */
   private function parseModuleUrl() {
      $matches = array();//(?:(?P<lang>[a-z]{2})\/)?
      if(!preg_match("/module\/(?:(?P<lang>[a-z]{2})\/)?(?P<category>[a-z0-9_-]+)\/(?P<action>[a-z0-9_-]+)\.(?P<output>[a-z0-9_-]+)\??(?P<params>[^?]+)?/i", self::$fullUrl, $matches)) {
         return false;
      }
      $this->category = $matches['category'];
      $this->name = $matches['category'];
      $this->action = $matches['action'];
      $this->outputType = $matches['output'];
      $this->lang = $matches['lang'];
      if(isset ($matches['params'])) {
         $this->parmas = $matches['params'];
      }
      return true;
   }

   /**
    * Metoda zkontroluje jesli se nejedná o url k akci epluginu (např. při ajax requestu)
    */
   private function parseComponentUrl() {
      $matches = array();
      if(!preg_match("/component\/(?P<name>[a-z0-9_-]+)\/(?:(?P<lang>[a-z]{2})\/)?(?P<category>[a-z0-9_-]+)\/(?P<action>[a-z0-9_-]+)\.(?P<output>[a-z0-9_-]+)\??(?<params>[^?]+)?/i", self::$fullUrl, $matches)) {
         return false;
      }
      $this->category = $matches['category'];
      $this->name = $matches['name'];
      $this->action = $matches['action'];
      $this->outputType = $matches['output'];
      $this->lang = $matches['lang'];
      if(isset ($matches['params'])) {
         $this->parmas = $matches['params'];
      }
      return true;
   }

   /**
    * Metoda zkontroluje jesli se nejedná o url k akci jspluginu (např. při ajax
    * requestu, dynamickému seznamu, atd)
    */
   private function parseJsPluginUrl() {
      $matches = array();
      if(!preg_match("/jsplugin\/(?P<name>[a-z0-9_-]+)\/(?:(?P<lang>[a-z]{2})\/)?(?P<category>[a-z0-9_-]+)\/(?P<action>[a-z0-9_-]+)\.(?P<output>[a-z0-9_-]+)\??(?P<params>[^?]+)?/i", self::$fullUrl, $matches)) {
         return false;
      }
      $this->category = $matches['category'];
      $this->name = $matches['name'];
      $this->action = $matches['action'];
      $this->outputType = $matches['output'];
      $this->lang = $matches['lang'];
      if(isset ($matches['params'])) {
         $this->parmas = $matches['params'];
      }
      return true;
   }

   /**
    * Metody vrací typ výstupu (html, js, php, ...)
    * @return string
    */
   public function getOutputType() {
      return $this->outputType;
   }

   /**
    * Metody vrací klíč kategorie
    * @return string
    */
   public function getCategory() {
      return $this->category;
   }

   /**
    * Metoda vrací část URL pro zpracování v modulu
    * @return string
    */
   public function getModuleUrlPart() {
      return $this->moduleUrlPart;
   }

   /**
    * Metoda vrací část url s parametry
    * @return string
    */
   public function getUrlParams() {
      return $this->parmas;
   }

   /**
    * Metoda vrací část url s jazykem aplikace
    * @return string
    */
   public function getUrlLang() {
      return $this->lang;
   }

   /**
    * Metoda vrací část url názvem pluginu nebo specielní stránky
    * @return string
    */
   public function getName() {
      return $this->name;
   }

   /**
    * Metoda vrací část url s názvem akce pluginu, modulu nebo specielní stránky
    * @return string
    */
   public function getAction() {
      return $this->action;
   }

   /**
    * Metoda kontroluje jestli se nejedná o url s ajax akcí
    */
   //   private static function checkAjaxFileUrl() {
   //      if(ereg('^ajax/(module|eplugin)/([^/.]+)_([[:digit:]]+).php', self::$fullUrl)) {
   //         self::$isAjax = true;
   //         return true;
   //      }
   //      return false;
   //   }

   /**
    * Metoda parsuje celou url do pole s jednotlivými proměnými
    */
   private function parseUrl() {
   //		Rozdělíme řetězec podle separátorů
   //      $urlItems = $urlItemsBack = array();
   //      //		Odstranění posledního lomítka
   //      $url = ereg_replace("^(.*)/$", "\\1", self::$currentUrl);
   //      $urlItems = $urlItemsBack = explode(URL_SEPARATOR, $url);
   //      reset($urlItems); // reset pole aby bylo na začátku
   //      $isCategory = $lang = false;
   //      //		Kontrola jestli je zadán jazyk
   //      if(Links::checkLangUrlRequest(pos($urlItems))) {
   //         $lang = true;
   //         unset ($urlItems[key($urlItems)]);
   //      }
   //      //		Kontrola jestli je zadána kategorie
   //      if(Links::checkCategoryUrlRequest(pos($urlItems))) {
   //         $isCategory = true;
   //         unset ($urlItems[key($urlItems)]);
   //      }
   //      // zjištění, jestli je nějáká kategorie nebo se jedná o neexistující stránku
   //      if(!$lang AND !$isCategory AND $urlItemsBack[0] != null) {
   //         AppCore::setErrorPage(); // zapnem Chybovou stránku
   //         return false;
   //      }
   //      //  Kontrola předání cesty pokud je definována
   //      if(Links::checkRouteUrlRequest(pos($urlItems))) {
   //         unset ($urlItems[key($urlItems)]);
   //      }
   //      //		Načetní článku FORMAT: "nazev-id"
   //      if(Links::checkArticleUrlRequest(pos($urlItems))) {
   //         unset ($urlItems[key($urlItems)]);
   //      }
   //      //		Načtení akce FORMAT: nazev_{action name (např. char)}-id_item
   //      if(Links::checkActionUrlRequest(pos($urlItems))) {
   //         unset ($urlItems[key($urlItems)]);
   //      }
   //      //		Načtení typu media FORMAT: media{typ media např www,print}
   //      $matches = array();
   //      $expresion = '^'.self::PARAM_MEDIA_TYPE_PREFIX.'([a-zA-Z]+)$';
   //      if(eregi($expresion, pos($urlItems), $matches)) {
   //         self::$media = $matches[1];
   //         self::$currentMediaUrlPart = pos($urlItems);
   //         unset ($urlItems[key($urlItems)]);
   //      }
   //      //        Vybrání ostatních  parametrů
   //      if(isset($urlItems) AND pos($urlItems) != null) {
   //         Links::chackOtherUrlParams($urlItems);
   //      }
   }

   /**
    * Metoda parsuje url pro Supported Services (eplugins, jsplugins, sitemp, atd.)
    */
   private static function parseUrlForsupportedServices() {
   //		Odstranění posledního lomítka
   //      $url = ereg_replace("^(.*)/$", "\\1", self::$fullUrl);
   //      $name = $file = $params = null;
   //      $regexResult = array();
   //      switch (self::$supportedServicesType) {
   //         case self::SUPPORTSERVICES_EPLUGIN_NAME:
   //            ereg('^(eplugin[^\./]*)/([^\.]*\.js)\?(.*)$', $url, $regexResult);
   //            $name = $regexResult[1];
   //            $file = $regexResult[2];
   //            $params = $regexResult[3];
   //            break;
   //         case self::SUPPORTSERVICES_JSPLUGIN_NAME:
   //            ereg('^(jsplugin[^\./]*)/([^\.]*\.js)\?(.*)$', $url, $regexResult);
   //            $name = $regexResult[1];
   //            $file = $regexResult[2];
   //            if(isset ($regexResult[3])) {
   //               $params = $regexResult[3];
   //            }
   //            break;
   //         case self::SUPPORTSERVICES_SITEMAP_NAME:
   //            ereg(self::$notNormalUrlArrayRegex[self::SUPPORTSERVICES_SITEMAP_NAME], $url, $regexResult);
   //            $name = $regexResult[1];
   //         default:
   //            break;
   //      }
   //      self::$supporteServicesName = $name;
   //      self::$supporteServicesFile = $file;
   //      self::$supporteServicesParams = $params;
   }

   /**
    * Metoda parsuje specialní url
    */
   private function parseSpecialUrl() {
   //      $regexArr = array();
   //      //		Rozdělíme řetězec podle separátorů
   //      $urlItems = $urlItemsBack = array();
   //      //		Odstranění posledního lomítka
   //      $url = ereg_replace("^(.*)/$", "\\1", self::$currentUrl);
   //      $urlItems = $urlItemsBack = explode(URL_SEPARATOR, $url);
   //      reset($urlItems); // reset pole aby bylo na začátku
   //      // jazyk
   //      if(Links::checkLangUrlRequest(pos($urlItems))) {
   //         $lang = true;
   //         unset ($urlItems[key($urlItems)]);
   //      }
   //      if(ereg(''.self::$specialPagesRegex[self::SPECIAL_PAGE_SEARCH].'$', $urlItems[key($urlItems)], $regexArr)) {
   //         self::$specialPageName = self::SPECIAL_PAGE_SEARCH;
   //         Search::factory($regexArr[1], $regexArr[2]);
   //      } else if(ereg(''.self::$specialPagesRegex[self::SPECIAL_PAGE_SITEMAP].'$', $urlItems[key($urlItems)], $regexArr)) {
   //            self::$specialPageName = self::SPECIAL_PAGE_SITEMAP;
   //         }
   }

   /**
    * Metoda parsuje url pro ajax akci
    */
   private static function parseAjaxUrl() {
   //      $regexResult = array();
   //      ereg('^ajax/(module|eplugin)/([^/.]+)_([^/._]+)_([[:digit:]]+).php\??(.*)$', self::$fullUrl,$regexResult);
   //      Ajax::setType($regexResult[1]);
   //      Ajax::setName($regexResult[2]);
   //      Ajax::setAction($regexResult[3]);
   //      Ajax::setIdItem($regexResult[4]);
   //      if (isset ($regexResult[5])) {
   //         Ajax::setParams($regexResult[5]);
   //      }
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
   //   public static function getMediaType() {
   //      return self::$media;
   //   }

   /**
    * Metoda vrací URl část řetězce s nastaveným typem média
    * @return string -- url část
    */
   //   public static function getCurrentMediaUrlPart() {
   //      return self::$currentMediaUrlPart;
   //   }

   /**
    * Metoda zevolí typ kontroleru modulu
    */
   public function choseController() {
   //		Vyvoříme objekt článku kvůli zjišťování přítomnosti článku
   //      $article = new Article();
   //      //		pokud není akce
   //      if(!$this->moduleAction->isAction()) {
   //      //			pokud je vybrán článek
   //         if($article->isArticle() AND !$this->moduleAction->isSomeAction()) {
   //            $action = strtolower($this->moduleAction->getDefaultArticleAction());
   //         }
   //         //			Pokud není vybrán článek
   //         else {
   //            $action = strtolower(AppCore::MODULE_MAIN_CONTROLLER_PREFIX);
   //         }
   //      }
   //      //		Pokud je vybrána akce
   //      else {
   //         $action = $this->moduleAction->getSelectedAction();
   //      }
   //      //		Přiřazení routy
   //      if($this->moduleRoutes->isRoute()) {
   //         $action = ucfirst($action);
   //         $action = $this->moduleRoutes->getRoute().$action;
   //      }
   //      return $action;
   }

/**
 * Metoda vrací pokud je nastavena SupportedServices
 * @return bool true pokud se zpracovává
 */
//   public static function isSupportedServices() {
//      return self::$isSupportedServices;
//   }

/**
 * Metoda vrací typ SupportedServices (Eplugin, JsPlugin, ...)
 * @return string -- konstanta SUPPORTED_SERVICES_XXX
 */
//   public static function getSupportedServicesType() {
//      return self::$supportedServicesType;
//   }

/**
 * Metoda vrací název SupportedServices (Epluginu, JsPluginu, ...)
 * @return string -- název služby, záleží na typu (např. tinymce, ...)
 * u typu Sitemap vrací xml nebo txt
 */
//   public static function getSupportedServicesName() {
//      return self::$supporteServicesName;
//   }

/**
 * Metoda vrací název souboru SupportedServices (Epluginu, JsPluginu, ...)
 * @return string -- název souboru
 */
//   public static function getSupportedServicesFile() {
//      return self::$supporteServicesFile;
//   }

/**
 * Metoda vrací parametry SupportedServices, všechny co jsou za otazníkem
 * @return string -- string parametry, nerozparsované nebo null
 */
//   public static function getSupportedServicesParams() {
//      return self::$supporteServicesParams;
//   }

/**
 * Metoda vrací pokud je nastavena SpecialPage
 * @return bool true pokud se zpracovává
 */
//   public static function isSpecialPage() {
//      if(self::$specialPageName == null) {
//         return false;
//      }
//      return true;
//   }

/**
 * Metoda vrací název speciální stránky
 * @return string -- konstanta SPECIAL_PAGE_XXX
 */
//   public static function getSpecialPage() {
//      return self::$specialPageName;
//   }

/**
 * Metoda vrcí regulerní výraz pro specielní stránku
 * @param string $pageType -- typ specielní stránky constanta SPECIAL_PAGE_XXX
 * @return string -- regulerní výraz
 */
//   public static function getSpecialPageRegexp($pageType) {
//      return self::$specialPagesRegex[$pageType];
//   }

/**
 * Metoda vrací jestli je spuštěn ajax požadavek
 * @return boolean -- true pokud je ajax požadavek
 */
//   public static function isAjaxRequest() {
//      return self::$isAjax;
//   }
}
?>
