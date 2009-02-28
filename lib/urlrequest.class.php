<?php
/**
 * Description of UrlRequest
 * Třída slouží pro parsování a obsluhu požadavků v URL adrese.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.5.0 $Revision: $
 * @author			$Author: $ $Date:$
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract		Třída pro obsluhu a UrlReqestu
 */
class UrlRequest {
     /**
      * Název parametru s typem media
      * @var string
      */
   const PARAM_MEDIA_TYPE_PREFIX = 'media';

     /**
      * Typ media pro web
      * @var string
      */
   const MEDIA_TYPE_WWW	= 'www';

     /**
      * Typ media pro web
      * @var string
      */
   const MEDIA_TYPE_PRINT	= 'print';

     /**
      * Přenosový protokol
      * @var string
      */
   const TRANSFER_PROTOCOL = 'http://';

     /**
      * $GET proměná s název epluginu
      * @var string
      */
   const GET_EPLUGIN_NAME = 'eplugin';
   
   /**
    * Název Supported Services EPluginu
    */
   const SUPPORTSERVICES_EPLUGIN_NAME = 'eplugin';
     /**
      * $GET proměná s název jspluginu
      * @var string
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
      * @var string
      */
   const URL_SEPARATOR = '/';

     /**
      * Obsahuje typ media
      * @var array
      */
   private static $media = null;

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
      * Objekt zvoleného modulu
      * @var Module
      */
   private $module = null;

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
    * Pole s parametry supproted services
    * @var string
    */
   private static $supporteServicesArray = array(self::SUPPORTED_SERVICES_ARR_NAME => null,
      self::SUPPORTED_SERVICES_ARR_PARAMS => null);

   /**
    * Konstruktor
    */
   public function  __construct(Action $action, Routes $routes) {
      $this->module = AppCore::getSelectedModule();
      $this->moduleAction = $action;
      $this->moduleRoutes = $routes;
   }

     /**
      * Metoda inicializuje požadavky v URL
      */
   public static function factory() {
//      echo $_SERVER['REQUEST_URI'];
      //		Vytvoření url
      self::createUrl();

      //		Parsování url
      if(self::checkNormalUrl()){
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

      //		Kontrola jestli je zadán jazyk
      if(Links::checkLangUrlRequest(pos($urlItems))){
         unset ($urlItems[key($urlItems)]);
      }

      //		Kontrola jestli je zadána kategorie
      $isCategory = false;
      if(Links::checkCategoryUrlRequest(pos($urlItems))){
         unset ($urlItems[key($urlItems)]);
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
         //          next($urlItems);
      }

      //        Vybrání ostatních  parametrů
      if(isset($urlItems) AND pos($urlItems) != null){
         Links::chackOtherUrlParams($urlItems);
      }

      // zjištění, jestli je nějáká kategorie nebo se jedná o neexistující stránku
      if(!$isCategory AND !empty ($urlItemsBack)){
         AppCore::setErrorPage(); // zapnem Chybovou stránku
      }
   }

   /**
    * Metoda parsuje url pro Supported Services (eplugins, jsplugins, sitemp, atd.)
    */
   private static function parseUrlForsupportedServices() {
      //		Odstranění posledního lomítka
      $url = ereg_replace("^(.*)/$", "\\1", self::$currentUrl);

      $name = null;
      $params = null;
      switch (self::$supportedServicesType) {
         case self::SUPPORTSERVICES_EPLUGIN_NAME:
            $name = ereg('^eplugin([^\.]*)\.js.*$', $url);
            $params = ereg('^eplugin[^\.]*.js\?(.*)$', $url);
            break;
         case self::SUPPORTSERVICES_JSPLUGIN_NAME:
            $name = ereg('^jsplugin([^\.]*)\.js.*$', $url);
            $params = ereg('^jsplugin[^\.]*.js\?(.*)$', $url);
            break;
         case self::SUPPORTSERVICES_SITEMAP_NAME:
            $name = ereg(self::$notNormalUrlArrayRegex[self::SUPPORTSERVICES_SITEMAP_NAME], $url);
            default:
               break;
         }



         self::$supporteServicesArray[self::SUPPORTED_SERVICES_ARR_NAME]= $name;
         self::$supporteServicesArray[self::SUPPORTED_SERVICES_ARR_PARAMS]= $params;

      }

     /**
      * Metoda vytvoří url a uloží ji do $currentUrl
      */
      private static function createUrl() {
         //		echo '<pre>';
         //		echo $_SERVER['PHP_SELF'].'<br>';
         //		echo $_SERVER['DOCUMENT_ROOT'].'<br>';
         //		echo $_SERVER['REQUEST_URI'].'<br>';
         //		echo $_SERVER['SCRIPT_NAME'].'<br>';
         //		echo AppCore::getAppWebDir().'<br>';
         //		echo '</pre>';

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
      public static function getSupportedServicesName() {
         return self::$supporteServicesArray[self::SUPPORTED_SERVICES_ARR_NAME];
      }

      /**
       * Metoda vrací parametry SupportedServices, všechny co jsou za otazníkem
       * @return string -- string parametry, nerozparsované nebo null
       */
      public static function getSupportedServicesParams() {
         return self::$supporteServicesArray[self::SUPPORTED_SERVICES_ARR_PARAMS];
      }

     /**
      * Metoda zjišťuje jestli byl nastaven index na eplugin
      *
      * @return boolean -- true pokud se má zpracovávat eplugin
      */
//      public static function isEplugin() {
//         return ;
//      }

     /**
      * Metoda vrací název zvoleného epluginu
      *
      * @return string -- název epluginu
      */
//      public static function getSelEpluginName() {
//         return rawurldecode($_GET[self::GET_EPLUGIN_NAME]);
//      }

     /**
      * Metoda zjišťuje jestli byl nastaven index na jsplugin
      *
      * @return boolean -- true pokud se má zpracovávat jsplugin
      */
//      public static function isJsplugin() {
//         if(isset($_GET[self::GET_JSPLUGIN_NAME])){
//            return true;
//         } else {
//            return false;
//         }
//      }

     /**
      * Metoda vrací název zvoleného jspluginu
      *
      * @return string -- název jspluginu
      */
//      public static function getSelJspluginName() {
//         return rawurldecode($_GET[self::GET_JSPLUGIN_NAME]);
//      }
   }
   ?>
