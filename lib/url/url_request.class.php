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
   const URL_TYPE_ENGINE_PAGE = 'specialpage';
   const URL_TYPE_MODULE_REQUEST = 'module';
   const URL_TYPE_MODULE_STATIC_REQUEST = 'modules';
   const URL_TYPE_COMPONENT_REQUEST = 'component';
   const URL_TYPE_JSPLUGIN_REQUEST = 'jsplugin';
   const URL_TYPE_SUPPORT_SERVICE = 'supportservice';

   /**
    * jestli se jedná o normální url nebo url s podpůrnými službami
    * @var string
    */
   private $urlType = self::URL_TYPE_NORMAL;

   /**
    * Proměnná obsahuje jestli je zpracovávána celá stránka, nebo jenom jeden požadavek
    * @var bool
    */
   private $pageFull = true;

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
    * Obsahuje část s url bez baseWebUrl
    * @var string
    */
    private static $webUrl = null;

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
   private $params = null;

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
    * Proměnná obsahuje jestli se jedná o požadavek typu XHR
    * @var boolean
    */
   private static $isXHRRequest = false;

   /**
    * Konstruktor
    */
   public function  __construct() {
      $this->checkUrlType();
   }

   /**
    * Metoda inicializuje požadavky v URL
    */
   public static function factory() {
   //		Vytvoření url
      $fullUrl = $_SERVER['REQUEST_URI'];
      self::$scriptName = $_SERVER["SCRIPT_NAME"];
//      $serverName = $_SERVER["SERVER_NAME"]; server_name udává bez www
      $serverName = $_SERVER["HTTP_HOST"];
      if(VVE_USE_SUBDOMAIN_HTACCESS_WORKAROUND != null){
         $fullUrl = str_replace(VVE_USE_SUBDOMAIN_HTACCESS_WORKAROUND, '', $fullUrl);
         self::$scriptName = str_replace(VVE_USE_SUBDOMAIN_HTACCESS_WORKAROUND, '', self::$scriptName);
      }
      self::$serverName = $serverName;
      //		Najdeme co je cesta k aplikaci a co je předaná url
      self::$fullUrl = substr($fullUrl, strpos(self::$scriptName, AppCore::APP_MAIN_FILE));
      // odstraníme dvojté lomítka
      self::$fullUrl = preg_replace('/[\/]{2,}/', '/', self::$fullUrl);
      //		Vytvoříme základní URL cestu k aplikaci
      $positionLastChar=strrpos(self::$scriptName, self::URL_SEPARATOR);
      self::$baseWebUrl=self::$transferProtocol.self::$serverName
          .substr(self::$scriptName, 0, $positionLastChar).self::URL_SEPARATOR;
      self::$webUrl = str_replace(self::$baseWebUrl, '', self::$fullUrl);
      if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
          self::$isXHRRequest = true;
      }
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
    * @todo dodělat lepší optimalizaci
    */
   public function checkUrlType() {
      $validRequest = false;
      // jesli se zpracovává soubor modulu
      if($this->parseSupportServiceUrl()
          OR $this->parseComponentUrl()
          OR $this->parseJsPluginUrl() OR $this->parseNormalUrl()
          OR $this->parseModuleStaticUrl()) {

         $validRequest = true;
         AppCore::setErrorPage(false);
         Url_Link::setCategory($this->getCategory());
         Url_Link_Module::setRoute($this->getModuleUrlPart());
         Url_Link::setParams($this->getUrlParams());
         Locale::setLang($this->getUrlLang());
         Url_Link::setLang($this->getUrlLang());
      }
   }

   /**
    * Meto zjišťuje, zda se jedná o Normální URL nebo URl jiného typu
    * (component, jsplugin, sitemap atd.)
    * @return boolean true pokud se jedná o normální url
    */
   private function parseNormalUrl() {
      if(self::$fullUrl == null) {
         return true;
      }

      // pokud není žádná adresa (jen jazyk)
      $return = false;
      // nastavení jazyku
      $match = array('url' => null);
      if(preg_match("/^(?:(?P<lang>[a-z]{2})\/)?(?P<url>.*)/", self::$fullUrl, $match) ){
         if(!empty ($match)) {
            $this->lang = $match['lang'];
            Locale::setLang($this->lang);
            Url_Link::setLang(Locale::getLang());
            $return = true;
         }
      }
      if(!empty ($match['url'])) {
         $return = false;
         // načtení kategorií
         $modelCat = new Model_Category();
         $categories = $modelCat->getCategoryList();
         foreach ($categories as $key => $cat) {
            if((string)$cat->{Model_Category::COLUMN_URLKEY} == null) continue;
            if (strpos($match['url'], (string)$cat->{Model_Category::COLUMN_URLKEY}) !== false) {
               $matches = array();
               $regexp = "/".str_replace('/', '\/', (string)$cat->{Model_Category::COLUMN_URLKEY})
                   ."\/(?P<other>[^?]*)\/?\??(?P<params>.*)/i";

               if(preg_match($regexp, $match['url'], $matches)) {
                  // pokud obsahuje soubor
                  $fileMatchs = array();
                  if(preg_match('/(?P<file>[a-z0-9]+)\.(?P<ext>[a-z0-9]+)/i', $matches['other'], $fileMatchs)){
                     $this->urlType = self::URL_TYPE_MODULE_REQUEST;
                     $this->outputType = $fileMatchs['ext'];
                     $this->pageFull = false;
                  } else if(self::isXHRRequest()){ // při XHR není nutné zpracovávat celou stráánku :-)
                     $this->urlType = self::URL_TYPE_MODULE_REQUEST;
                     $this->pageFull = false;
                  }
                  // jinak se jednná o kategorii
                  $this->category = (string)$cat->{Model_Category::COLUMN_URLKEY};
                  $this->moduleUrlPart = $matches['other'];
                  $this->params = $matches['params'];
                  $return = true;
                  break;
               }
            }
         }
      }
      return $return;
   }

   /**
    * Metoda zkontroluje jesli se nejedná o specialní stránky obsažené v enginu
    * (hledání, sitemap, atd)
    */
   private function parseSupportServiceUrl() {
      $regexps = array('/^(?:(?P<lang>[a-z]{2})\/)?(?P<name>sitemap).(?P<output>(xml|txt|html)+)/i');
      foreach ($regexps as $regex) {
         $matches = array();
         if(preg_match($regex, self::$webUrl, $matches) != 0) {
            $this->pageFull = false;
            if(isset ($matches['output'])) {
               $this->outputType = $matches['output'];
               if($matches['output'] == 'html') $this->pageFull = true;
            }
            if(isset ($matches['action'])) {
               $this->outputType = $matches['action'];
            }
            $this->name = $matches['name'];
            $this->urlType = self::URL_TYPE_SUPPORT_SERVICE;
            return true;
         }
      }
      return false;
   }

   /**
    * Metoda zkontroluje jesli se nejedná o url k statické akci modulu (např. při ajax requestu)
    */
   private function parseModuleStaticUrl() {
      $matches = array();//(?:(?P<lang>[a-z]{2})\/)?
      if(!preg_match("/module_s\/(?:(?P<lang>[a-z]{2})\/)?(?P<mname>[\/a-z]+)\/(?P<action>[a-z0-9_-]+)\.(?P<output>[a-z0-9_-]+)\??(?P<params>[^?]+)?/i", self::$fullUrl, $matches)) {
         return false;
      }
      $this->category = null;
      $this->name = $matches['mname'];
      $this->action = $matches['action'];
      $this->outputType = $matches['output'];
      $this->lang = $matches['lang'];
      if(isset ($matches['params'])) {
         $this->params = $matches['params'];
      }
      $this->urlType = self::URL_TYPE_MODULE_STATIC_REQUEST;
      $this->pageFull = false;
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
         $this->params = $matches['params'];
      }
      $this->urlType = self::URL_TYPE_COMPONENT_REQUEST;
      $this->pageFull = false;
      return true;
   }

   /**
    * Metoda zkontroluje jesli se nejedná o url k akci jspluginu (např. při ajax
    * requestu, dynamickému seznamu, atd)
    */
   private function parseJsPluginUrl() {
      $matches = array();
//      if(!preg_match("/jsplugin\/(?P<name>[a-z0-9_-]+)\/(?:(?P<lang>[a-z]{2})\/)?(?P<category>[a-z0-9_-]+)\/(?P<action>[a-z0-9_-]+)\.(?P<output>[a-z0-9_-]+)\??(?P<params>[^?]+)?/i", self::$fullUrl, $matches)) {
      if(!preg_match("/jsplugin\/(?P<name>[a-z0-9_-]+)\/(?:(?P<lang>[a-z]{2})\/)?cat-(?P<category>[0-9]+)\/(?P<action>[a-z0-9_-]+)\.(?P<output>[a-z0-9_-]+)\??(?P<params>[^?]+)?/i", self::$fullUrl, $matches)) {
         return false;
      }
      $this->category = $matches['category'];
      $this->name = $matches['name'];
      $this->action = $matches['action'];
      $this->outputType = $matches['output'];
      $this->lang = $matches['lang'];
      if(isset ($matches['params'])) {
         $this->params = $matches['params'];
      }
      $this->urlType = self::URL_TYPE_JSPLUGIN_REQUEST;
      $this->pageFull = false;
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
      return $this->params;
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
    * Metoda vrací jestli se zpracovává celá celá stránka, nebo jenom část
    * @return bool -- true pokud je zpracovávána stránka
    */
   public function isFullPage() {
      return $this->pageFull;
   }

   /**
    * Metoda vrací základní URL cestu k aplikaci
    * @return string
    */
   public static function getBaseWebDir() {
      return self::$baseWebUrl;
   }

   /**
    * Metoda vrací jestli se jedná o požadavek typu XHR (ajax)
    * @return boolean
    */
   public static function isXHRRequest() {
      return self::$isXHRRequest;
   }
}
?>
