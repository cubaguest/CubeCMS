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
      if(isset($_SERVER["HTTPS"])){
         self::$transferProtocol = "https://";
      }
      
      if(self::$serverName != 'localhost'){
         $pos = strpos(self::$serverName, '.');
         self::$domain = substr(self::$serverName, $pos+1);
         //self::$subDomain = substr(self::$serverName, 0, -strlen(self::$domain)-1);
      }

      if(VVE_SUB_SITE_DOMAIN != null AND VVE_SUB_SITE_USE_HTACCESS == true){ // Only if Htacces subdomain workarround
         $fullUrl = str_replace(VVE_SUB_SITE_DOMAIN, '', $fullUrl);
         $scriptName = str_replace('/'.VVE_SUB_SITE_DOMAIN, '', $scriptName);
      }

      //		Vytvoříme základní URL cestu k aplikaci
      self::$baseWebUrl = self::$baseMainWebUrl = self::$transferProtocol.self::$serverName.substr($scriptName, 0, strpos($scriptName, '/')).'/';
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
      // zjištění jazykové verze
      $match = array(2 => null);
      if(preg_match("/^(?:(".implode("|", Locales::getAppLangs()).")\/)?(.*)/", self::$fullUrl, $match) ){
         if(!empty ($match) && isset($match[1])) {
            $this->lang = $match[1];
            Locales::setLang($this->lang); // musí být tady kvůli načtení jazyka
         }
      }
      // jesli se zpracovává soubor modulu
      if($this->parseCoreModuleUrl($match[2])
          OR $this->parseComponentUrl()
          OR $this->parseJsPluginUrl() 
          OR $this->parseNormalUrl($match[2])
          OR $this->parseModuleStaticUrl()) {

         $validRequest = true;
         AppCore::setErrorPage(false);
         Url_Link::setCategory($this->getCategory());
         if($this->getUrlType() == self::URL_TYPE_CORE_MODULE){
            Url_Link::setCategory($this->getCategory().'.'.$this->getOutputType());
         }
         Url_Link_Module::setRoute($this->getModuleUrlPart());
         Url_Link::setParams($this->getUrlParams());
         if($this->getUrlLang() != null){
            Locales::setLang($this->getUrlLang());
            Url_Link::setLang($this->getUrlLang());
         }
      }
   }

   /**
    * Meto zjišťuje, zda se jedná o Normální URL nebo URl jiného typu
    * (component, jsplugin, sitemap atd.)
    * @return boolean true pokud se jedná o normální url
    */
   private function parseNormalUrl($urlPart) {
      if($urlPart == null) {
         return true;
      }
      // pokud není žádná adresa (jen jazyk)
      $return = false;
      if(strpos($urlPart, 'admin') !== 0){ // pokud je obsaženo jako první slovo admin > jedná se o admin kategorii a není nutné procházen normální
         // načtení kategorií
         $cache = new Cache();
         $cacheKey = md5('_cats_'.Auth::getGroupId().Locales::getLang()."_".$urlPart);
         
         if( ($cat = $cache->get($cacheKey)) == false){
            $modelCat = new Model_Category();
            $cat = $modelCat
               ->columns(
                     array(Model_Category::COLUMN_URLKEY,
                        'urlpart' => 'REPLACE(:urlpartfull, '.Model_Category::COLUMN_URLKEY.'_'.Locales::getLang().', \'\')'), 
                     array('urlpartfull' => $urlPart))
               ->where('INSTR(:url, '.Model_Category::COLUMN_URLKEY.'_'.Locales::getLang().') = 1', 
                     array( 'url' => $urlPart ))
               ->order(array('LENGTH('.Model_Category::COLUMN_URLKEY.')' => Model_ORM::ORDER_DESC))
               ->record();
            $cache->set($cacheKey, $cat);
         } 
         
         if($cat != false && !$cat->isNew()){
            $matches = array();
            $regexp = "/\/([^?]*)\/?\??(.*)/i";
            if(preg_match($regexp, $cat->urlpart, $matches)) {
               // pokud obsahuje soubor
               $fileMatchs = array();
               if($matches[1] == self::URL_FILE_RSS){
                  $this->urlType = self::URL_TYPE_MODULE_RSS;
                  $this->name = 'rss';
                  $this->outputType = 'xml';
                  $this->pageFull = false;
               } else if($matches[1] == self::URL_FILE_ATOM){
                  $this->urlType = self::URL_TYPE_MODULE_RSS;
                  $this->name = 'atom';
                  $this->outputType = 'xml';
                  $this->pageFull = false;
               } else if(preg_match('/([a-z0-9]+)\.([a-z0-9]+)/i', $matches[1], $fileMatchs)){
                  $this->urlType = self::URL_TYPE_MODULE_REQUEST;
                  $this->outputType = $fileMatchs[2];
                  $this->pageFull = false;
               } else if(self::isXHRRequest() || isset ($_GET['out'])){ // při XHR není nutné zpracovávat celou stránku :-)
                  $this->urlType = self::URL_TYPE_MODULE_REQUEST;
                  if(isset ($_GET['out'])){
                     $this->outputType = $_GET['out'];
                  }
                  $this->pageFull = false;
               }
               // jinak se jednná o kategorii
               $this->category = (string)$cat->{Model_Category::COLUMN_URLKEY};
               $this->moduleUrlPart = $matches[1];
               $this->params = $matches[2];
               $return = true;
            }
         }
      }
      // kontrola admin kategorie
      if(Auth::isAdmin()){
         $model = new Model_CategoryAdm();
         $cats = $model->getCategoryList();
         unset($model);
         foreach ($cats as $cat) {
            if (strpos($urlPart, (string)$cat->{Model_Category::COLUMN_URLKEY}) !== false) {
               $matches = array();
               $regexp = "/".str_replace('/', '\/', (string)$cat->{Model_Category::COLUMN_URLKEY})."\/([^?]*)\/?\??(.*)/i";
               if(preg_match($regexp, $urlPart, $matches)) {
                  // pokud obsahuje soubor
                  $fileMatchs = array();
                  if(preg_match('/([a-z0-9]+)\.([a-z0-9]+)/i', $matches[1], $fileMatchs)){
                     $this->urlType = self::URL_TYPE_MODULE_REQUEST;
                     $this->outputType = $fileMatchs[2];
                     $this->pageFull = false;
                  } else if(self::isXHRRequest()){ // při XHR není nutné zpracovávat celou stránku :-)
                     $this->urlType = self::URL_TYPE_MODULE_REQUEST;
                     $this->pageFull = false;
                  }
                  // jinak se jednná o kategorii
                  $this->category = (string)$cat->{Model_Category::COLUMN_URLKEY};
                  $this->moduleUrlPart = $matches[1];
                  $this->params = $matches[2];
                  $this->isAdminCat = true;
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
   private function parseCoreModuleUrl($urlPart) {
      $return = false;
      $regexp = '/^((?:sitemap|rss|autorun)).((xml|txt|html|php)+)/i';
      $matches = array();
      if(preg_match($regexp, $urlPart, $matches) != 0) {
         $this->pageFull = false;
         $this->urlType = self::URL_TYPE_CORE_MODULE;
         $this->outputType = $matches[2];
         $this->category = $matches[1];
         if($matches[2] == 'html' AND !self::isXHRRequest()){
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
      $matches = array();
      //                             1 lang        2 mname     3 action       4 output        5 params
      if(!preg_match("/module_s\/(?:([a-z]{2})\/)?([\/a-z]+)\/([a-z0-9_-]+)\.([a-z0-9_-]+)\??([^?]+)?/i", self::$fullUrl, $matches)) {
         return false;
      }
      $this->category = null;
      $this->name = $matches[2];
      $this->action = $matches[3];
      $this->outputType = $matches[4];
      $this->lang = $matches[1];
      if(isset ($matches[5])) {
         $this->params = $matches[5];
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
      //                           ?P<name>          ?P<lang>      ?P<category>   ?P<action>     ?P<output>      ?<params>
      if(!preg_match("/component\/([a-z0-9_-]+)\/(?:([a-z]{2})\/)?([a-z0-9_-]+)\/([a-z0-9_-]+)\.([a-z0-9_-]+)\??([^?]+)?/i", self::$fullUrl, $matches)) {
         return false;
      }
      $this->category = $matches[3];
      $this->name = $matches[1];
      $this->action = $matches[4];
      $this->outputType = $matches[5];
      $this->lang = $matches[2];
      if(isset ($matches[6])) {
         $this->params = $matches[6];
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
      if(!preg_match("/jsplugin\/([a-z0-9_-]+)\/(?:([a-z]{2})\/)?cat-([0-9]+)\/([a-z0-9_-]+)\.([a-z0-9_-]+)\??([^?]+)?/i", 
         self::$fullUrl, $matches)) {
         return false;
      }
      $this->category = $matches[3];
      $this->name = $matches[1];
      $this->action = $matches[4];
      $this->outputType = $matches[5];
      $this->lang = $matches[2];
      if(isset ($matches[6])) {
         $this->params = $matches[6];
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
