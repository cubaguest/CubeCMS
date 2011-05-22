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
 * @todo          Přepsat na statickou třídu
 */
class Url_Request {
/**
 * Oddělovač částí URL adresy
 */
   const URL_TYPE_NORMAL = 'normal';
   const URL_TYPE_ENGINE_PAGE = 'specialpage';
   const URL_TYPE_CORE_MODULE = 'coremodule';
   const URL_TYPE_MODULE_REQUEST = 'module';
   const URL_TYPE_MODULE_RSS = 'modulerss';
   const URL_TYPE_MODULE_STATIC_REQUEST = 'modules';
   const URL_TYPE_COMPONENT_REQUEST = 'component';
   const URL_TYPE_JSPLUGIN_REQUEST = 'jsplugin';
   const URL_TYPE_SUPPORT_SERVICE = 'supportservice';

   const URL_FILE_RSS = 'rss.xml';
   const URL_FILE_ATOM = 'atom.xml';


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
    * Základní URL adresa aplikace bez subdomény
    * @var string
    */
   private static $baseMainWebUrl = null;

   /**
    * Doména bez www (domain.com)
    * @var string
    */
   private static $domain = 'localhost';

   /**
    * Adresa serveru (např. www.seznam.cz)
    * @var string
    */
   private static $serverName = null;

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
    * jestli se jedná o admin kategorii
    * @var bool
    */
   private $isAdminCat = false;

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
      $scriptName = $_SERVER["SCRIPT_NAME"];
      self::$serverName = $_SERVER["HTTP_HOST"];

      if(self::$serverName != 'localhost'){
         $matches = array();
         preg_match("/[^\.\/]+\.[^\.\/]+$/", self::$serverName, $matches);
         self::$domain = $matches[0];
      }

      if(VVE_SUB_SITE_DOMAIN != null AND VVE_SUB_SITE_USE_HTACCESS == true){ // Only if Htacces subdomain workarround
         // @TODO Work corectly?
         $fullUrl = str_replace(VVE_SUB_SITE_DOMAIN, '', $fullUrl);
         $scriptName = str_replace(VVE_SUB_SITE_DOMAIN, '', $scriptName);
      }

      //		Vytvoříme základní URL cestu k aplikaci
      self::$baseWebUrl = self::$baseMainWebUrl = self::$transferProtocol.self::$serverName.substr($scriptName, 0, strrpos($scriptName, '/')).'/';
      if(VVE_SUB_SITE_DOMAIN != null){
         self::$baseMainWebUrl = str_replace(self::$serverName, 'www.'.self::$domain, self::$baseWebUrl);
      }
//    Najdeme co je cesta k aplikaci a co je předaná url
      self::$fullUrl = substr($fullUrl, strpos($scriptName, AppCore::APP_MAIN_FILE));
      // odstraníme dvojté lomítka
      self::$fullUrl = preg_replace('/[\/]{2,}/', '/', self::$fullUrl);

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
      if($this->parseCoreModuleUrl()
          OR $this->parseComponentUrl()
          OR $this->parseJsPluginUrl() OR $this->parseNormalUrl()
          OR $this->parseModuleStaticUrl()) {

         $validRequest = true;
         AppCore::setErrorPage(false);
         Url_Link::setCategory($this->getCategory());
         if($this->getUrlType() == self::URL_TYPE_CORE_MODULE){
            Url_Link::setCategory($this->getCategory().'.'.$this->getOutputType());
         }
         Url_Link_Module::setRoute($this->getModuleUrlPart());
         Url_Link::setParams($this->getUrlParams());
         Locales::setLang($this->getUrlLang());
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
            Locales::setLang($this->lang);
            Url_Link::setLang(Locales::getLang());
            $return = true;
         }
      }
      if(!empty ($match['url'])) {
         $return = false;
         if(strpos($match['url'], 'admin') !== 0){ // pokud je obsaženo jako první slovo admin > jedná se o admin kategorii a není nutné procházen normální
            // načtení kategorií
            $modelCat = new Model_Category();
            $categories = $modelCat->getCategoryList();
            unset($modelCat);
            foreach ($categories as $cat) {
               if((string)$cat->{Model_Category::COLUMN_URLKEY} == null) continue;
//                Debug::log($match['url']);
               if (strpos($match['url'], (string)$cat->{Model_Category::COLUMN_URLKEY}) !== false) {
                  $matches = array();
                  $regexp = "/".str_replace('/', '\/', (string)$cat->{Model_Category::COLUMN_URLKEY})
                     ."\/(?P<other>[^?]*)\/?\??(?P<params>.*)/i";

                  if(preg_match($regexp, $match['url'], $matches)) {
                     // pokud obsahuje soubor
                     $fileMatchs = array();
                     if($matches['other'] == self::URL_FILE_RSS){
                        $this->urlType = self::URL_TYPE_MODULE_RSS;
                        $this->name = 'rss';
                        $this->outputType = 'xml';
                        $this->pageFull = false;
                     } else if($matches['other'] == self::URL_FILE_ATOM){
                        $this->urlType = self::URL_TYPE_MODULE_RSS;
                        $this->name = 'atom';
                        $this->outputType = 'xml';
                        $this->pageFull = false;
                     } else if(preg_match('/(?P<file>[a-z0-9]+)\.(?P<ext>[a-z0-9]+)/i', $matches['other'], $fileMatchs)){
                        $this->urlType = self::URL_TYPE_MODULE_REQUEST;
                        $this->outputType = $fileMatchs['ext'];
                        $this->pageFull = false;
                     } else if(self::isXHRRequest()){ // při XHR není nutné zpracovávat celou stránku :-)
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

         // kontrola admin kategorie
         if(Auth::isAdmin()){
            $model = new Model_CategoryAdm();
            $cats = $model->getCategoryList();
            unset($model);
            foreach ($cats as $cat) {
               if (strpos($match['url'], (string)$cat->{Model_Category::COLUMN_URLKEY}) !== false) {
                  $matches = array();
                  $regexp = "/".str_replace('/', '\/', (string)$cat->{Model_Category::COLUMN_URLKEY})."\/(?P<other>[^?]*)\/?\??(?P<params>.*)/i";
                  if(preg_match($regexp, $match['url'], $matches)) {
                     // pokud obsahuje soubor
                     $fileMatchs = array();
                     /*if($matches['other'] == self::URL_FILE_RSS){
                        $this->urlType = self::URL_TYPE_MODULE_RSS;
                        $this->name = 'rss';
                        $this->outputType = 'xml';
                        $this->pageFull = false;
                     } else if($matches['other'] == self::URL_FILE_ATOM){
                        $this->urlType = self::URL_TYPE_MODULE_RSS;
                        $this->name = 'atom';
                        $this->outputType = 'xml';
                        $this->pageFull = false;
                     } else*/
                     if(preg_match('/(?P<file>[a-z0-9]+)\.(?P<ext>[a-z0-9]+)/i', $matches['other'], $fileMatchs)){
                        $this->urlType = self::URL_TYPE_MODULE_REQUEST;
                        $this->outputType = $fileMatchs['ext'];
                        $this->pageFull = false;
                     } else if(self::isXHRRequest()){ // při XHR není nutné zpracovávat celou stránku :-)
                        $this->urlType = self::URL_TYPE_MODULE_REQUEST;
                        $this->pageFull = false;
                     }
                     // jinak se jednná o kategorii
                     $this->category = (string)$cat->{Model_Category::COLUMN_URLKEY};
                     $this->moduleUrlPart = $matches['other'];
                     $this->params = $matches['params'];
                     $this->isAdminCat = true;
                     $return = true;
                     break;
                  }
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
   private function parseCoreModuleUrl() {
      $return = false;
      $match = array('url' => null);
      if(preg_match("/^(?:(?P<lang>[a-z]{2})\/)?(?P<url>.*)/", self::$fullUrl, $match) ){
         if(!empty ($match)) {
            $this->lang = $match['lang'];
            Locales::setLang($this->lang);
            Url_Link::setLang(Locales::getLang());
         }
      }
      $regexp = '/^(?P<name>(?:sitemap|rss)).(?P<output>(xml|txt|html)+)/i';
      $matches = array();
      if(preg_match($regexp, $match['url'], $matches) != 0) {
         $this->pageFull = false;
         $this->urlType = self::URL_TYPE_CORE_MODULE;
         $this->outputType = $matches['output'];
         $this->category = $matches['name'];
         if($matches['output'] == 'html' AND !self::isXHRRequest()){
            $this->pageFull = true;
         }
         $return = true;
      }
   return $return;
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
    * Metoda vrací jestli se zpracovává admin kategorie
    * @return bool -- true pokud je admin kategorie
    */
   public function isAdminCategory() {
      return $this->isAdminCat;
   }

   /**
    * Metoda vrací základní URL cestu k aplikaci
    * @return string
    */
   public static function getBaseWebDir($returnMainWeb = false) {
      if($returnMainWeb){
         return self::$baseMainWebUrl;
      }
      return self::$baseWebUrl;
   }

   /**
    * Metoda vrací aktuální celou url
    * @return string
    */
   public static function getCurrentUrl() {
      return self::$transferProtocol.self::$serverName.$_SERVER['REQUEST_URI'];
   }

   /**
    * Metoda vrací část s požadavkem
    * @return string
    */
   public static function getRequestUrl() {
      return self::$fullUrl;
   }

   /**
    * Metoda vrací hlavní doménu
    * @return string
    */
   public static function getDomain() {
      return self::$domain;
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
