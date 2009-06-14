<?php
/**
 * Abstraktní třída pro Engine Pluginy - EPlugins.
 * Třída obsahuje základní metody pro vytváření EPluginu a práci s nimi
 * (např. scroll, comments, atd.).
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Abstraktní třída pro vytvoření Epluginu
 * @todo 			implementovat generování názvu souborů pro zvolený eplugin
 */

class Eplugin {
   /**
    * Výchozí cesta s šablonama
    * @var string
    */
   const EPLUGINS_DEFAULT_TEMPALTES_DIR = Template::TEMPLATES_DIR;

   /**
    * Parametr pro přenos souboru js pluginu
    */
   const PARAMS_EPLUGIN_FILE_PREFIX = 'eplugin';

   /**
    * Objekt šablony
    * @var Template
    */
   protected $template = null;

   /**
    * Objekt se systémovými informacemi o modulu
    * @var Module_Sys
    */
   private $sys = null;

   /**
    * Konstruktor třídy, spouští metodu init();
    */
   function __construct(Module_Sys $sys = null, $paramsForInit = null, $paramsForRun = null){
      if($sys != null AND $sys instanceof Module_Sys){
         $this->sys = $sys;
      } else {
         $this->sys = new Module_Sys();
      }

      if(!UrlRequest::isAjaxRequest() AND !UrlRequest::isSupportedServices()){
         $this->template = new Template();
         $this->init($paramsForInit);
         $this->run($paramsForRun);
         $this->initTemplate();
         //$this->view();
      }
   }

   /**
    * Metoda nastavuje knihovnu epluginu je použita v epluginech
    */
   public function setParams() {}

   /**
    * Metoda inicializuje spuštění epluginu pouze jako sjednoho souboru
    */
   public function initRunOnlyEplugin() {
      $this->runOnlyEplugin(UrlRequest::getSupportedServicesFile(),
         UrlRequest::getSupportedServicesParams());
   }

   /**
    * Metoda se využívá pro načtení proměných do stránky,
    * je volána při volání parametru stránky pro EPlugin
    *
    */
   public function runOnlyEplugin($fileName, $fileParams = null){}

   /**
    * Inicializační metoda EPluginu
    * @param mixed $params -- parametry epluginu (pokud je třeba)
    */
   protected function init($params = null){}

   /**
    * Spuštění pluginu
    * @param mixed $params -- parametry epluginu (pokud je třeba)
    */
   protected function run($params = null) {}

   /**
    * Metoda inicializuje šablonu
    */
   protected function initTemplate() {}

   /**
    * Metoda nastaví id šablony pro výpis
    * @param integer -- id šablony (jakékoliv)
    */
   protected function view() {}

   /**
    * Metoda vrací objekt k tvorbě odkazů
    *
    * @return Links -- objekt odkazů
    */
   protected function getLinks($clear = false, $onlyWebRoot = false){
      $link = new Links($clear, $onlyWebRoot);
      $cat = AppCore::getSellectedCategory();
      if($cat != false){
         $link->category($cat[Model_Category::COLUMN_CAT_LABEL], $cat[Model_Category::COLUMN_CAT_ID]);
      }
      return $link;
   }

   /**
    * Metoda vrací systémový objekt modulu
    *
    * @return Module_Sys -- objekt modulu
    */
   protected function sys(){
      return $this->sys;
   }

   /**
    * Metoda vrací objekt modulu
    *
    * @return Module -- objekt modulu
    */
   protected function module(){
      return $this->sys()->module();
   }


   /**
    * Metoda vrací objekt k připojení k db
    *
    * @return DbInterface -- objekt Db
    */
   protected function getDb(){
      return AppCore::getDbConnector();
   }

   /**
    * Metoda vrací objekt ke konfiguraci enginu
    *
    * @return Config -- objekt Config
    */
   protected function getSysConfig(){
      return AppCore::sysConfig();
   }

   /**
    * Metoda vrací objekt autorizace a právům k modulům
    *
    * @return Rights -- objekt Rights
    */
   protected function rights(){
      return $this->sys()->rights();
   }

   /**
    * Metoda vrací objekt chbových zpráv
    *
    * @return Messages -- objekt chybových zpráv
    */
   protected function errMsg(){
      return AppCore::getUserErrors();
   }

   /**
    * Metoda vrací objekt informačních zpráv
    *
    * @return Messages -- objekt informačních zpráv
    */
   protected function infoMsg(){
      return AppCore::getInfoMessages();
   }

   /**
    * Metoda vrací název epluginu
    * @return string -- název epluginu
    */
   public final function getEpluginName() {
      return strtolower(get_class($this));
//      $name = strtolower(get_class($this));
//      return str_ireplace(self::PARAMS_EPLUGIN_FILE_PREFIX, '', $name);
   }

   /**
    * Metoda vrací objekt šablony
    * @return Template -- objekt šablony
    */
   final public function template(){
      return $this->template;
   }

   /**
    * Metoda vrací odkaz na soubor epluginu
    * //TODO možná implementovat vracení odkazu na EPlugin file (./epluginuserimages.js)
    */
   public function getFileLink($file = null, $params = null) {
      if($file == null) {
         $file = '.'.URL_SEPARATOR.$this->getEpluginName()
         .URL_SEPARATOR.$this->getEpluginName().'js';
         if($params != null AND is_array($params)) {
            $param = http_build_query($params);
            $file.='?'.$param;
         }
      } else if(is_string($file)) {
         $file = '.'.URL_SEPARATOR.$this->getEpluginName().URL_SEPARATOR.$file;
         if($params != null AND is_array($params)) {
            $param = http_build_query($params);
            $file.='?'.$param;
         }
      } else if($file instanceof JsPlugin_JsFile){
         $file = '.'.URL_SEPARATOR.$this->getEpluginName().URL_SEPARATOR.$file;
      }
      return $file;
   }

   /**
    * Metoda nastaví id šablony pro výpis
    * @param integer -- id šablony (jakékoliv)
    */
   final public function renderEplugin() {
      $this->view();
      $this->template()->renderTemplate();
   }

   /**
    * Metoda vrací id šablony
    * @return integer
    */
   //   protected function getIdTpl() {
   //      return $this->idTpl;
   //   }

   /**
    * Metoda nastaví podnázev šablony pro výpis
    * @param string -- podnázev šablony (jakékoliv)
    */
   //   public function setTplSubName($name) {
   //      self::$tplSubName[$this->getIdTpl()] = $name;
   //   }

   /**
    * Metoda vrací podnázev šablony
    * @return string
    */
   //   protected function getTplSubName() {
   //      if(isset(self::$tplSubName[$this->getIdTpl()])){
   //         return self::$tplSubName[$this->getIdTpl()];
   //      } else {
   //         return null;
   //      }
   //   }

   /**
    * Metoda nastavuje do epluginu objkek autorizace
    * @param Auth $auth -- objekt autorizace
    */
//      public function setAuthParam(Auth $auth) {
//
//         $this->sys()->setRights(new Rights(null));
//         $this->sys()->setRights(new Rights(null));
//      }
}
?>