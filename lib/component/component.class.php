<?php
/**
 * Abstraktní třída pro komponenty -- Components
 * Třída obsahuje základní metody pro vytváření komponent a práci s nimi
 * (např. scroll, comments, atd.).
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: component.class.php 636 2009-07-07 20:17:18Z jakub $ VVE 6.0 $Revision: 636 $
 * @author        $Author: jakub $ $Date: 2009-07-07 20:17:18 +0000 (Út, 07 čec 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-07-07 20:17:18 +0000 (Út, 07 čec 2009) $
 * @abstract 		Abstraktní třída pro vytvoření komponenty
 * @todo 			implementovat generování názvu souborů pro zvolenou komponentu
 */

class Component {
/**
 * Výchozí cesta s šablonama
 * @var string
 */
   const COMPONENTS_DEFAULT_TEMPALTES_DIR = Template::TEMPLATES_DIR;

   /**
    * Objekt šablony
    * @var Template
    */
   protected $template = null;

   /**
    * Název komponenty
    * @var string
    */
   private $componentName = null;

   /**
    * Objekt odkazů
    * @var Url_Link_Component
    */
   private $componentLink = null;

   /**
    * Objekt odkazů
    * @var Url_Link
    */
   private $pageLink = null;

   /**
    * Id článku, ke které se bude komponenta vztahovat
    * @var integer
    */
   private $idArticle = null;

   /**
    * Pole s parametry pluginu, přenesenými přes url adresu
    * @var array
    */
   private $pluginParams = array();

   /**
    * Pole s konfiguračními hodnotami
    * @var array
    */
   protected $config = array();

   /**
    * Konstruktor třídy, spouští metodu init();
    */
   function __construct($runOnly = false) {
      $this->componentName = str_ireplace(__CLASS__.'_', '', get_class($this));
      $this->componentLink =  new Url_Link_Component($this->componentName);
      $this->pageLink =  new Url_Link();
      $this->componentLink->category(Category::getSelectedCategory()->getUrlKey());
      $this->init();
      if(!$runOnly) {
         $this->template = new Template_Component($this->componentLink, $this->pageLink);
      }
   }

   /**
    * Metoda pro nasatvování komponenty
    * @param string $name -- název konfigurační hodnoty
    * @param mixed $value -- hodnota konfigurační hodnoty
    * @return Component
    */
   public function setConfig($name, $value) {
      $this->config[$name] = $value;
      return $this;
   }

   /**
    * Metoda vrací konfiguraci komponenty
    * @param string $name -- název konfigurační hodnoty
    */
   public function getConfig($name) {
      if(isset ($this->config[$name])){
         return $this->config[$name];
      } else {
         return null;
      }
   }

   /**
    * Metoda nastaví id článku
    * @param integer $id
    */
   public function setIdArticle($id) {
      $this->idArticle = $id;
   }

   /**
    * Metoda vrací nastavené Id článku
    * @return integer -- id článku
    */
   public function getIdArticle() {
      return $this->idArticle;
   }

   /**
    * Metoda pro spuštění componenty pouze jako jednoho souboru. Vybere potřebné
    * akce a spustí zadané metody
    */
   public function runAction($actionName, $params, $outputType) {
   // inicializační metoda
      $this->init();

      $this->pluginParams = $params;
      if(method_exists($this, $actionName.ucfirst($outputType).'Controller') AND
          method_exists($this, $actionName.ucfirst($outputType).'View')) {
         $this->{$actionName.ucfirst($outputType).'Controller'}();
         $this->{$actionName.ucfirst($outputType).'View'}();
      } else if(method_exists($this, $actionName.'Controller') AND
             method_exists($this, $actionName.'View')) {
            $this->{$actionName.'Controller'}();
            $this->{$actionName.'View'}();
         } else {
            trigger_error(_('Neimplementována metoda Componenty'));
         }
   }

   /**
    * Inicializační metoda componenty, spouští se po vytvoření instance componenty
    */
   protected function init() {}

   /**
    * Spuštění pluginu
    * @param mixed $params -- parametry epluginu (pokud je třeba)
    */
   public function mainController() {}

   /**
    * Metoda nastaví id šablony pro výpis
    * @param integer -- id šablony (jakékoliv)
    */
   public function mainView() {}

   /**
    * Metoda pro spouštění některých akcí přímo v kontroleru
    */
   public function runCtrlPart() {}

   /**
    * Metoda vrací objekt k tvorbě odkazů
    *
    * @return Url_Link_Component -- objekt odkazů
    */
   protected function link() {
      return $this->componentLink;
   }

   /**
    * Metoda vrací objekt k tvorbě odkazů odpovídající dané stránce
    *
    * @return Url_Link -- objekt odkazů
    */
   protected function pageLink() {
      return $this->pageLink;
   }

   /**
    * Metoda vrací objekt chbových zpráv
    *
    * @return Messages -- objekt chybových zpráv
    */
   protected function errMsg() {
      return AppCore::getUserErrors();
   }

   /**
    * Metoda vrací objekt informačních zpráv
    *
    * @return Messages -- objekt informačních zpráv
    */
   protected function infoMsg() {
      return AppCore::getInfoMessages();
   }

   /**
    * Metoda vrací název komponenty
    * @return string -- název komponenty
    */
   public final function getComponentName() {
      return $this->componentName;
   }

   /**
    * Metoda vrací objekt šablony
    * @return Template_Component -- objekt šablony
    */
   final public function template() {
      return $this->template;
   }

   /**
    * Metoda vykreslí komponentu
    * @todo ??????
    */
   final public function renderComponent() {
      print ($this);
   }

   public function  __toString() {
      $this->mainView();
      return (string)$this->template();
   }
}
?>