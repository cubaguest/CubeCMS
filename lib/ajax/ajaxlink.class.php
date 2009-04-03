<?php
/**
 * Třída pro tvorbu odkazů pro ajaxové načítání
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE3.5.0 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro tvorbu ajaxových odkazů
 */
class AjaxLink {
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
   const AJAX_DEFAULT_ACTION = 'default';

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
    * Název ajaxového modulu nebo epluginu
    * @var string
    */
   private $ajaxFile = null;

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
    * Konstruktor vytváří základní odkaz
    */
   public function  __construct($handler) {
      $this->link = new Links();
      $this->link->clear(true);
      if($handler instanceof EPlugin){
         $this->setEplugin($handler->getEpluginName());
      } else if($handler instanceof Module){
         $this->setModule($handler->getName());
      } else {
         throw new InvalidArgumentException(_('Nepodporovaný typ ajaxové funkce'));
      }
   }

   /**
    * Metoda nastaví název modulu pro prováděný script
    * @param string $name -- název modulu
    */
   private function setModule($name) {
      $this->ajaxFile = self::AJAX_MODULE_NAME.strtolower($name).'.php';
   }

   /**
    * Metoda nastaví název epluginu pro prováděný script
    * @param string $name -- název epluginu
    */
   private function setEplugin($name) {
      $this->ajaxFile = self::AJAX_EPLUGIN_NAME.strtolower($name).'.php';
   }

   /**
    * Metoda přidává parametr
    * @param UrlParam $param -- parametr
    */
   public function addParam($param, $value) {
      $this->ajaxFileParams[$param] = $value;
   }

   /**
    * Metoda nastavuje akci pro ajax soubor
    * @param string $action -- název akce
    */
   public function setAction($action) {
      $this->ajaxAction = $action;
   }

   /**
    * Metoda vrací soubor pro zpracování ajaxem
    * @return string -- název souboru
    */
   public function getFile() {
      return (string)$this->link.self::AJAX_LINK_PREFIX.$this->ajaxFile;
   }

   /**
    * Metoda vrací řetězec parametrů pro ajax
    * @return string -- pole parametrů
    */
   public function getParams() {
      $string = null;
      if($this->ajaxAction != null){
         $string .= self::AJAX_ACTION_INDEX.'='.$this->ajaxAction;
         if(!empty ($this->ajaxFileParams)){
            $string .= '&';
         }
      }
      $string .= http_build_query($this->ajaxFileParams);
      return $string;
   }

   /**
    * Metoda vytvoří řetěuzec z odkazu
    * @return string -- vrací odkaz na ajaxový soubor
    */
   public function  __toString() {
      $string = (string)$this->link.self::AJAX_LINK_PREFIX.$this->ajaxFile.'?';
      if($this->ajaxAction != null){
         $string .= self::AJAX_ACTION_INDEX.'='.$this->ajaxAction;
         if(!empty ($this->ajaxFileParams)){
            $string .= '&';
         }
      }
      $string .= http_build_query($this->ajaxFileParams);
      return $string;
   }
}
?>
