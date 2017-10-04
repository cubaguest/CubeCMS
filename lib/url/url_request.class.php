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
   const URL_TYPE_MODULE_PROCESS = 'processModule';
   const URL_TYPE_MODULE_PROCESS_DO = 'action';

   const URL_FILE_RSS = 'rss.xml';
   const URL_FILE_ATOM = 'atom.xml';
   
   const URL_POPUP_WINDOW = 'popupwindow';
   
   const URL_OUTPUT_TYPE = '_cms_output';

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
    * podadresář aplikace
    * @var string
    */ 
   private static $subdir = null;
   
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
    * jestli má být u kategorie použito id místo url klíče
    * @var bool
    */
   private $categoryUseUrlKey = true;

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
    * Proměnná obsahuje jestli se má požadavek vynutit jako XHR
    * @var boolean
    */
   private static $XHRRespondClass = false;

   private static $instance = false;

   /**
    * Konstruktor
    */
   protected function  __construct() {
      $this->checkUrlType();
   }

   /**
    * Vrací instanci
    * @return Url_Request
    */
   public static function getInstance()
   {
      if(!self::$instance){
         self::$instance = new self();
      }
      return self::$instance;
   }

   /**
    * Metoda inicializuje požadavky v URL
    */
   public static function factory() {
      self::$domain = $_SERVER['SERVER_NAME'];
      // část mezi doménou a adresářem aplikace
      $baseAndWebPathDiff = str_replace(DIRECTORY_SEPARATOR, '/', str_replace(CUBE_CMS_BASE_DIR, '', CUBE_CMS_WEB_DIR));
      $urlPathPart = ltrim(str_replace(
              array( 'index.php',$baseAndWebPathDiff), 
              array( '', ''), 
              $_SERVER['SCRIPT_NAME']
              ), '/');
      
      
      if(isset($_SERVER["HTTPS"])){
         self::$transferProtocol = "https://";
         Url_Link::setTransferProtocol(self::$transferProtocol);
      }
      
      // základní url adresa systému
      self::$baseMainWebUrl = self::$transferProtocol.CUBE_CMS_PRIMARY_DOMAIN.'/';
      
      // url adresa webu
      self::$baseWebUrl = self::$transferProtocol.self::$domain.'/'.$urlPathPart;
      
      if(CUBE_CMS_BASE_DIR == CUBE_CMS_WEB_DIR){
         self::$baseMainWebUrl = self::$baseWebUrl;
      }
      
      // url za kořenem webu
      self::$fullUrl = str_replace($urlPathPart, '', ltrim(str_replace('index.php','',$_SERVER['REQUEST_URI']), '/'));
      self::$subdir = $urlPathPart;
      
      // pokud je subdoména pomocí htaccess
      if($urlPathPart != null && $baseAndWebPathDiff != null){
         self::$baseWebUrl .= $baseAndWebPathDiff;
         self::$fullUrl = str_replace($baseAndWebPathDiff, '', self::$fullUrl);
         self::$subdir = $urlPathPart.$baseAndWebPathDiff;
      }
      /*
       * self::$subdir           - podsložka aplikace - tedy její kořen       /myappinfolder/
       * self::$serverName       - webserver - tedy doména                    www.vkci.com
       * self::$fullUrl          - cesta za doménou                           ucet/?debug 
       * self::$webUrl           - cesta za doménou                           ucet/?debug  
       * self::$baseMainWebUrl   - základní cestak aplikaci (root app)        http://www.vkci.com/
       * self::$baseWebUrl       - základní cestak aplikaci                   http://www.vkci.com/
       * self::$domain           - doména bez subdomén                        vkci.com
       */
      
      if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
          || isset($_GET['SIMULATE_XHR'])) {
          self::$isXHRRequest = true;
      }
      if(isset($_POST['_cubecms_respond_class']) || isset($_GET['_cubecms_respond_class'])) {
          self::$isXHRRequest = true;
          self::$XHRRespondClass = isset($_POST['_cubecms_respond_class']) ? $_POST['_cubecms_respond_class'] : $_GET['_cubecms_respond_class'];
      }
   }

   /**
    * Metoda vrací typ url adresy (normal, eplugin, module, jsplugin, atd)
    * @return string -- typ url adresy
    */
   public function getUrlType() {
      return $this->urlType;
   }
   
   public static function detectLang()
   {
      $match = array(1 => null, 2 => null);
      if(preg_match("/^(?:(".implode("|", Locales::getAppLangs()).")\/)?(.*)/", self::$fullUrl, $match) && !empty ($match) && isset($match[1]) ){
         Locales::setLang($match[1]); // musí být tady kvůli načtení jazyka
         Url_Link::setLang(Locales::getLang());
         // remove lang part from url
         self::$fullUrl = $match[2];
      }
   }

   /**
    * Metoda zjistí typ url
    * @todo dodělat lepší optimalizaci
    */
   public function checkUrlType() {
      $validRequest = false;
      // zjištění jazykové verze
      // jesli se zpracovává soubor modulu
      if($this->parseCoreModuleUrl()
          OR $this->parseComponentUrl()
          OR $this->parseJsPluginUrl() 
          OR $this->parseNormalUrl()
          OR $this->parseModuleStaticUrl()) {

         $validRequest = true;
         AppCore::setErrorPage(false);
         Url_Link::setCategory($this->getCategory());
         if($this->getUrlType() == self::URL_TYPE_CORE_MODULE){
            Url_Link::setCategory($this->getCategory().'.'.$this->getOutputType());
         }
         Url_Link_Module::setRoute($this->getModuleUrlPart());
         Url_Link::setParams($this->getUrlParams());
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
      if(strpos(self::$fullUrl, 'admin') !== 0){ // pokud je obsaženo jako první slovo admin > jedná se o admin kategorii a není nutné procházen normální
         // načtení kategorií
         $cache = new Cache();
         $cacheKey = md5(self::$serverName.'_cats_'.Auth::getGroupId().Locales::getLang()."_".self::$fullUrl);

         $catMatches = array();
         $modelCat = new Model_Category();
         $isDirectCategory = false;
         if(preg_match('/^category-([0-9]+)\//', self::$fullUrl, $catMatches)){ // kategorie má předáno ID
            $cat = $modelCat->columns(array(
                '*',
                'urlpart' => '\''.  str_replace('category-'.$catMatches[1], '', self::$fullUrl).'\''
            ))->record($catMatches[1]);
            $isDirectCategory = true;
         } else if( ($cat = $cache->get($cacheKey)) == false){ // kategorie podle url klíče
            $modelCat = new Model_Category();
            $cat = $modelCat
               ->columns(
               array(Model_Category::COLUMN_URLKEY,
                  'urlpart' => 'TRIM(LEADING '.Model_ORM::getLangColumn(Model_Category::COLUMN_URLKEY).' FROM :urlpartfull)'),
               array('urlpartfull' => self::$fullUrl))
               ->where('INSTR(:url, '.Model_ORM::getLangColumn(Model_Category::COLUMN_URLKEY).') = 1',
               array( 'url' => self::$fullUrl ))
               ->order(array('LENGTH('.Model_Category::COLUMN_URLKEY.')' => Model_ORM::ORDER_DESC))
               ->record();
            $cache->set($cacheKey, $cat);
         } 
         if (!Auth::isAdmin()) {
            $modelCat->where(' AND '.Model_Category::COLUMN_DISABLE." = 0", array(), true);
         }
         
         $cat = $modelCat->record();
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
               } else if(self::isXHRRequest() || isset ($_GET['out']) || isset ($_REQUEST[self::URL_OUTPUT_TYPE])){ // při XHR není nutné zpracovávat celou stránku :-)
                  $this->urlType = self::URL_TYPE_MODULE_REQUEST;
                  if(isset ($_GET['out'])){
                     $this->outputType = $_GET['out'];
                  } else if(isset ($_REQUEST[self::URL_OUTPUT_TYPE])){
                     $this->outputType = $_REQUEST[self::URL_OUTPUT_TYPE];
                  }
                  $this->pageFull = false;
               }
               // jinak se jednná o kategorii
               $this->categoryUseUrlKey = !$isDirectCategory;
               $this->category = $isDirectCategory ? (int)$cat->getPK() : (string)$cat->{Model_Category::COLUMN_URLKEY};
               $this->moduleUrlPart = $matches[1];
               $this->params = $matches[2];
               $return = true;
            }
         }
      }
      // kontrola admin kategorie
      if(!$return && Auth::isAdmin()){
         $item = Model_CategoryAdm::findItemByUrl(self::$fullUrl);
         if($item){
            $regexp = "/".str_replace('/', '\/', (string)$item->{Model_Category::COLUMN_URLKEY})."\/([^?]*)\/?\??(.*)/i";
               if(preg_match($regexp, self::$fullUrl, $matches)) {
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
                  $this->outputType = isset($_REQUEST[self::URL_OUTPUT_TYPE]) ? $_REQUEST[self::URL_OUTPUT_TYPE] : $this->outputType;
                  // jinak se jednná o kategorii
                  $this->category = (string)$item->{Model_Category::COLUMN_URLKEY};
                  $this->moduleUrlPart = $matches[1];
                  $this->params = $matches[2];
                  $this->isAdminCat = true;
                  $return = true;
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
      // načtení seznamu interních modulů
      $modules = array();
      foreach (glob(AppCore::getAppLibDir().'lib'.DIRECTORY_SEPARATOR.'module'.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR) as $value) {
         $modules[] = basename($value);
      }
      $regexp = '/^((?:'. implode('|', $modules).')).((xml|txt|html|php)+)/i';
      $matches = array();
      if(preg_match($regexp, self::$fullUrl, $matches) != 0) {
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
      //                           2 mname     3 action       4 output              5 params
      if(!preg_match("/module_s\/([\/a-z]+)\/([a-z0-9_-]+)(?:\.([a-z0-9_-]+))?\/?\??([^?]+)?/i", self::$fullUrl, $matches)) {
         return false;
      }
      $this->category = null;
      $this->name = $matches[1];
      $this->action = $matches[2];
      $this->outputType = $matches[3];
      if(isset ($matches[4])) {
         $this->params = $matches[4];
      }
      $this->urlType = self::URL_TYPE_MODULE_STATIC_REQUEST;
      $this->outputType == null ? $this->pageFull = true : $this->pageFull = false;
      return true;
   }

   /**
    * Metoda zkontroluje jesli se nejedná o url k akci epluginu (např. při ajax requestu)
    */
   private function parseComponentUrl() {
      $matches = array();
      //                              ?P<name>       ?P<category>   ?P<action>     ?P<output>      ?<params>
      if(!preg_match("/component\/([a-z0-9_-]+)\/([a-z0-9_-]+)\/([a-z0-9_-]+)\.([a-z0-9_-]+)\??([^?]+)?/i", self::$fullUrl, $matches)) {
         return false;
      }
      $this->category = $matches[2];
      $this->name = $matches[1];
      $this->action = $matches[3];
      $this->outputType = $matches[4];
      if(isset ($matches[5])) {
         $this->params = $matches[5];
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
      if(!preg_match("/jsplugin\/([a-z0-9_-]+)\/cat-([0-9]+)\/([a-z0-9_-]+)\.([a-z0-9_-]+)\??([^?]+)?/i", 
         self::$fullUrl, $matches)) {
         return false;
      }
      $this->category = $matches[2];
      $this->name = $matches[1];
      $this->action = $matches[3];
      $this->outputType = $matches[4];
      if(isset ($matches[5])) {
         $this->params = $matches[5];
      }
      $this->urlType = self::URL_TYPE_JSPLUGIN_REQUEST;
      $this->pageFull = false;
      $this->isAdminCat = false;
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
    * Metody vrací klíč kategorie (buď vlastní nebo univerzální)
    * @return string
    */
   public function getCategoryUrlKey($urlkey, $id) {
      return $this->categoryUseUrlKey ? $urlkey : 'category-'.$id;
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
    * Metoda vrací jestli je daná stránka popup okno
    * @return bool
    */
   public static function isPopupWindow() {
      return ( isset($_GET['popupwindow']) && $_GET['popupwindow'] == 1);
   }
   
   /**
    * Metoda vrací název callback funkce popup okna
    * @return bool
    */
   public static function getPopupWindowCallback() {
      return ( isset($_GET['callback']) ? $_GET['callback'] : null);
   }
   
   /**
    * MNetoda vrátí přenosový protokol (http:// nebo https://)
    * @return string
    */
   public static function getTransferProtocol()
   {
      return self::$transferProtocol;
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
   
   /**
    * Metoda vrací jestli se je definováne reposn třída XHR (ajax)
    * @return boolean
    */
   public static function isXHRRespondClass() {
      return self::$XHRRespondClass !== false ;
   }
   
   /**
    * Metoda vrací třídu pro XHR (ajax)
    * @return string
    */
   public static function getXHRRespondClass() {
      return self::$XHRRespondClass;
   }
   
   /**
    * Metoda podadresář aplikace
    * @return string
    */
   public static function getAppSubdir() {
      return self::$subdir;
   }
}
