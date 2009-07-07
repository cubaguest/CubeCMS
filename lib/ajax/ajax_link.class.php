<?php
/**
 * Třída pro tvorbu odkazů pro ajaxové načítání
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.5.0 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro tvorbu ajaxových odkazů
 */
class Ajax_Link {
   /**
    * Prefix odkazů
    */
   const AJAX_LINK_PREFIX = 'ajax';

   /**
    * Název funkce pro eplugin
    */
   const AJAX_EPLUGIN_NAME = 'eplugin';

   /**
    * Název pro ajax modulu
    */
   const AJAX_MODULE_NAME = 'module';

   /**
    * Výchozí akce pro ajax
    */
   const AJAX_DEFAULT_ACTION = 'main';

   /**
    * Název indexu pro akci
    */
   const AJAX_ACTION_INDEX = 'action';

   /**
    * Link pro ajax akci
    * @var Links
    */
   private $link = null;

   /**
    * Název modulu nebo epluginu ajax
    * @var string
    */
    private $ajaxName = null;

   /**
    * Id item pro který se bude request zpracovávat
    * @var string
    */
    private $ajaxIdItem = null;

   /**
    * Parametry ajax souboru
    * @var UrlParam
    */
   private $ajaxFileParams = array();

   /**
    * Název akce pro ajax soubor
    * @var string
    */
   private $ajaxAction = null;

   /**
    * Název typu ajax requestu
    */
    private $ajaxType = null;

   /**
    * Konstruktor vytváří základní odkaz
    */
   public function  __construct($handler) {
      $this->ajaxAction = self::AJAX_DEFAULT_ACTION;
      if($handler instanceof EPlugin){
         $this->ajaxType = self::AJAX_EPLUGIN_NAME;
         // odstranění prefixu 'eplugin_'
         $this->ajaxName = str_ireplace(self::AJAX_EPLUGIN_NAME.'_', null, $handler->getEpluginName());
         $this->ajaxIdItem = $handler->getIdItem();
      } else if($handler instanceof Module){
         $this->ajaxType = self::AJAX_MODULE_NAME;
         $this->ajaxName = $handler->label()->name();
         $this->ajaxIdItem = $handler->getId();
      } else {
         trigger_error(_('Nepodporovaný typ ajaxové funkce'), E_USER_WARNING);
      }
      $this->link = new Links();
      $this->link->clear(true,true);
   }

   /**
    * Metoda přidává parametr
    * @param UrlParam $param -- parametr
    * @return Ajax_Link
    */
   public function param($param, $value) {
      $obj = clone $this;
      $obj->ajaxFileParams[$param] = $value;
      return $obj;
   }

   /**
    * Metoda nastavuje akci pro ajax soubor
    * @param string $action -- název akce
    * @return Ajax_Link
    */
   public function action($action) {
//      $this->ajaxAction = $action;
      $obj = clone $this;
      $obj->ajaxAction = $action;
      return $obj;
   }

   /**
    * Metoda vrací název akce
    * @return string
    */
   private function getAction() {
      return $this->ajaxAction;
   }

   /**
    * Metoda vrací id item pro ajax request
    * @return integer
    */
   private function getIdItem() {
      return $this->ajaxIdItem;
   }

   /**
    * Metoda vrací soubor pro zpracování ajaxem
    * @return string -- název souboru
    */
   public function getFile() {
      return $this->link.self::AJAX_LINK_PREFIX.'/'.$this->ajaxType.'/'
      .$this->ajaxName.'_'.$this->getAction().'_'.$this->getIdItem().'.php';
   }

   /**
    * Metoda vrací řetězec parametrů pro ajax
    * @return string -- pole parametrů
    */
   public function getParams() {
      return http_build_query($this->ajaxFileParams);
   }

   /**
    * Metoda vytvoří řetěuzec z odkazu
    * @return string -- vrací odkaz na ajaxový soubor
    */
   public function  __toString() {
      $string = (string)$this->getFile();
      if(!empty ($this->ajaxFileParams)){
         $string .= '?'.http_build_query($this->ajaxFileParams);
      }
      return $string;
   }
}
?>
