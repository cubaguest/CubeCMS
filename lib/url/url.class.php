<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cuba
 * Date: 18.11.12
 * Time: 10:40
 * To change this template use File | Settings | File Templates.
 */
class Url
{
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
   protected $transferProtocol = 'http';

   /**
    * Proměná s hostem
    * @var string
    */
   protected $host = null;

   /**
    * Proměná s portem
    * @var string
    */
   protected $port = null;

   /**
    * Proměná s uživatelem
    * @var string
    */
   protected $user = null;

   /**
    * Proměná s heslem
    * @var string
    */
   protected $password = null;

   /**
    * Proměná s cestou
    * @var string
    */
   protected $path = null;

   /**
    * Soubor který se má zobrazit
    * @var string
    */
   protected $file = null;

   /**
    * Pole s parsovatelnými parametry v url
    * @var array
    */
   protected $paramsArray = array();

   /**
    * Kotva který se má zobrazit
    * @var string
    */
   protected $anchor = null;

   public function __construct($url = null)
   {
      $this->parseUrl($url != null ? $url : $_SERVER['HTTP_REFERER']);
   }

   /**
    * Metoda inicializuje odkazy
    *
    */
   protected function parseUrl($url) {
      $p = parse_url($url);
      $this->transferProtocol = $p['scheme'];
      $this->host = $p['host'];

      $this->port = isset($p['port']) ? $p['port'] : null;
      $this->user = isset($p['user']) ? $p['user'] : null;
      $this->password = isset($p['pass']) ? $p['pass'] : null;

      $this->paramsArray = isset($p['query']) ? self::parseParams($p['query']) : array();
      // path, file
      $pInfo = pathinfo($p['path']);
      if(isset($pInfo['extension'])){
         $this->path = $pInfo['dirname']."/";
         $this->file = $pInfo['basename'];
      } else {
         $this->path = $p['path'];
      }
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
    * @return Url_Link
    */
   public function rmParam($name = null) {
      if(is_array($name)){
         foreach ($name as $n) {
            if(array_key_exists($n, $this->paramsArray)){
               unset($this->paramsArray[$n]);
            }
         }
      } else {
         if($name != null && array_key_exists($name, $this->paramsArray)) {
            unset($this->paramsArray[$name]);
         }
         // Odstranění všch parametrů (normálových i obyčejných)
         else if( is_null($name) ) {
            $this->paramsArray = array();
         }
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
    * Metoda nastaví aktuální kotvu
    * @param string $anchor -- název kotvy
    */
   public function anchor($anchor = null) {
      $this->anchor = $anchor;
      return $this;
   }

   /**
    * Metoda vrací část se souborem pro url
    * @param string -- soubor
    */
   protected function getFile() {
      return $this->file;
   }

   /**
    * Metoda vrací část s kotvou v url
    * @param string -- kotva
    */
   protected function getAnchor() {
      if($this->anchor != null) {
         return '#'.$this->anchor;
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
    * Metoda odstraní všechny parametry v odkazu
    * @return Url_Link -- sám sebe
    */
   public function clear() {
      $this->rmParam();
      $this->path = null;
      $this->file = null;
      $this->anchor = null;
      return $this;
   }

   /**
    * Metoda vytvoří nový zpětný odkaz a vrátí předešlý odkaz s menším levelem
    * @param Url/string $noBackLink -- objekt odkazu pokud neexistuje zpětný
    * @return Url
    */
   public function back($link) {
      if(isset($_GET['back'])){
         $url = new Url($_GET['back']);
         return $url;
      }
      // pokud referer odkazuje na aktuální website
      if(isset($_SERVER['HTTP_REFERER']) && strpos(Url_Link::getMainWebDir(), $_SERVER['HTTP_REFERER']) == 0){ // je na začátku
         $url = new Url($_SERVER['HTTP_REFERER']);
         return $url;
      }

      if($link instanceof Url_Link){
         return $link;
      }
      return $this;
   }


   /**
    * Metoda parsuje normálové parametry a vrací je jako pole, kde klíč je název
    * a hodnota parametru je hodnota
    * @param string/array $params -- řetězec s parametry
    * @return array -- pole s parametry
    */
   protected static function parseParams($params) {
      $paramsArr = array();
      if(is_string($params) ){
         parse_str($params, $paramsArr);
      } else if(is_array($params)){
         $paramsArr = $params;
      }
//      if($params != null) {
//         $paramsArr = $_GET;
//         if(!function_exists('urlDecodeParam')){
//            function urlDecodeParam(&$param, $key) {
//               $param = urldecode($param);
//            }
//         }
//         array_walk_recursive($paramsArr, 'urlDecodeParam');
//      }
      return $paramsArr;
   }

   /**
    * Metoda vrací část s parametry pro url (parsovatelné)
    * @param string -- řetězec s parametry
    */
   protected function getParams() {
      $return = null;
      if(!empty( $this->paramsArray )) {
         $return = self::URL_SEPARATOR_LINK_PARAMS.http_build_query((array)$this->paramsArray);
      }
      return $return;
   }

   /**
    * Metoda nastavuje znovunahrání stránky
    * @param string -- externí odkaz na který se má přesměrovat (option)
    */
   public function reload($link = null, $code = 302) {
      $this->redirect($link, $code);
   }

   /**
    * Metoda přesměruje na zadnaou stránku
    * @param string -- externí odkaz na který se má přesměrovat (option)
    */
   public function redirect($link = null, $code = 302) {
      if(!Url_Request::isXHRRequest() && CoreErrors::isEmpty()){ // u XHR není nutný reload
         if ($link == null) {
            Template_Output::addHeader("Location: " . (string)$this, true, $code);
         } else {
            Template_Output::addHeader("Location: " . (string)$link, true, $code);
         }
         Template_Output::addHeader('X-XSS-Protection: 0');
         Template_Output::sendHeaders();
         session_commit(); // při více přesměrování se ztrácí info a err messages
         die;
      }
   }

   /**
    * Overloading
    */

   /**
    * Metoda pro převod na řetězec
    * @return string (ex: 'http://username:password@hostname/path?arg=value#anchor')
    */
   public function __toString()
   {
      $l = '';
      $l .= $this->transferProtocol."://";
      if($this->user != null){
         $l .= $this->user != null ? $this->user.( $this->password != null ? ":".$this->password : null ) : null;
         $l .= "@";
      }
      $l .= $this->host;
      $l .= ($this->port != null && $this->port != 80 ) ? ':'.$this->port : null;
      $l .= $this->path.$this->file;
      $l .= $this->getParams();
      $l .= $this->anchor != null ? '#'.$this->anchor : null;

      return $l;
   }

   public function __set($name, $value)
   {
      $this->paramsArray[$name] = $value;
   }

   public function __get($name)
   {
      if (array_key_exists($name, $this->paramsArray)) {
         return $this->paramsArray[$name];
      }
      return null;
   }

   public function __isset($name)
   {
      return isset($this->paramsArray[$name]);
   }

   public function __unset($name)
   {
      unset($this->paramsArray[$name]);
   }
}
