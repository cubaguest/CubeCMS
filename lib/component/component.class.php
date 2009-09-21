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
    * Parametr pro přenos souboru js pluginu
    */
   //   const PARAMS_EPLUGIN_FILE_PREFIX = 'eplugin';

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
   private $link = null;

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
    * Konstruktor třídy, spouští metodu init();
    */
   function __construct($runOnly = false) {
      $this->componentName = str_ireplace(__CLASS__.'_', '', get_class($this));
      $this->link =  new Url_Link_Component($this->componentName);
      $this->link->category(Category::getMainCategory()->getUrlKey());
      $this->init();
      if(!$runOnly) {
         $this->template = new Template_Component($this->link);
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
   protected function mainController() {}

   /**
    * Metoda nastaví id šablony pro výpis
    * @param integer -- id šablony (jakékoliv)
    */
   protected function mainView() {}

   /**
    * Metoda vrací objekt k tvorbě odkazů
    *
    * @return Url_Link_Component -- objekt odkazů
    */
   protected function link() {
      return $this->link;
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
    * Metoda vrací název epluginu
    * @return string -- název epluginu
    */
   public final function getEpluginName() {
      return strtolower(get_class($this));
   }

   /**
    * Metoda vrací objekt šablony
    * @return Template_Component -- objekt šablony
    */
   final public function template() {
      return $this->template;
   }

   /**
    * Metoda nastaví id šablony pro výpis
    * @param integer -- id šablony (jakékoliv)
    */
   final public function renderComponent() {
      print ($this);
   }

   public function  __toString() {
      $this->mainController();
      $this->mainView();

      return (string)$this->template();
   }
}
?>