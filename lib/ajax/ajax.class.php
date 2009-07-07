<?php
/**
 * Třída pro obsluhu ajaxových částí kódu
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.9.3 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract		Třída pro obsluhu Ajax Akcí v modulech a EPluginech
 */
class Ajax {
/**
 * Sufix pro ajaxové metody
 */
   const AJAX_METHOD_SUFIX = 'Ajax';

   /**
    * Proměnná obsahuje id item pro které je ajax request volán
    * @var integer
    */
   private static $ajaxIdItem = null;

   /**
    * Název prováděné akce
    * @var string
    */
   private static $ajaxAction = null;


   /**
    * Typ ajax požadavku (eplugin|module)
    * @var string
    */
   private static $ajaxType = null;

   /**
    * Název ajax modulu nebo epluginu
    * @var string
    */
   private static $ajaxName = null;

   /**
    * pole s parametry souboru ajaxu
    * @var string
    */
   private static $ajaxFileParams = array();

   /**
    * Konstruktor pro vytvoření objektu pro práci s ajaxem
    * @param string $fileParams -- řetězec parametrů předané scriptu
    */
   public function  __construct() {
   }

   /**
    * Metoda vrací parametr ajax souboru
    * @param string $name -- název parametru
    * @param mixed $defaultValue -- výchozí hodnota
    * @return mixed -- hodnota parametru nebo výchozí hodnota
    */
   public function getParam($name, $defaultValue = null) {
      if(isset($_POST[$name])) {
         return addslashes($_POST[$name]);
      } else if(isset (self::$ajaxFileParams[$name])) {
            return self::$ajaxFileParams[$name];
         } else {
            return $defaultValue;
         }
   }

   /**
    * Metoda vrací název akce ajaxu
    * @return string -- název akce (metody, která se  má provést)
    */
   public function getMetod() {
      return self::getAjaxAction().self::AJAX_METHOD_SUFIX;
   }

   /**
    * Metoda vrací id item ajax requestu
    * @return integer -- id item
    */
   public function getIdItem() {
      return self::getAjaxIdItem();
   }

   /**
    * Metoda nastaví typ ajax požadavku
    * @param string $type
    */
   public static function setType($type) {
      self::$ajaxType = $type;
   }

   /**
    * Metoda nastaví název ajax požadavku (název modulu nebo epluginu)
    * @param string $name
    */
   public static function setName($name) {
      self::$ajaxName = $name;
   }

   /**
    * Metoda nastaví název akce ajax požadavku
    * @param string $action
    */
   public static function setAction($action) {
      self::$ajaxAction = $action;
   }

   /**
    * Metoda nastaví id item ajax požadavku
    * @param integer $idItem
    */
   public static function setIdItem($idItem) {
      self::$ajaxIdItem = $idItem;
   }

   /**
    * Metoda rozparsuje a nastaví parametry ajax souboru
    * @param string $paramsString
    */
   public static function setParams($paramsString) {
      // parametry v URL
      if(!empty ($paramsString)) {
         $params = explode('&', $paramsString);
         $par = array();
         foreach ($params as $variable) {
            $p = explode('=', $variable);
            $par[$p[0]] = rawurldecode($p[1]);
         }

         if(isset ($par[Ajax_Link::AJAX_ACTION_INDEX])) {
            self::setAction($par[Ajax_Link::AJAX_ACTION_INDEX]);
            unset ($par[Ajax_Link::AJAX_ACTION_INDEX]);
         }
         self::$ajaxFileParams = $par;
      }

      // parametry předané přes POST
      if(!empty ($_POST)){
         self::$ajaxFileParams = array_merge_recursive(self::$ajaxFileParams, $_POST);
//         foreach ($_POST as $name => $param) {
//            self::$ajaxFileParams[$name] = addslashes($param);
//         }
      }
   }


   // OLD z UrlRequest

   /**
    * Metoda vrací typ ajax požadavku - (module, eplugin)
    * @return string
    */
   public static function getAjaxType() {
      return self::$ajaxType;
   }

   /**
    * Metoda vrací název ajax modulu nebo epluginu
    * @return string -- název
    */
   public static function getAjaxName() {
      return self::$ajaxName;
   }

   /**
    * Metoda vrací název akce ajax modulu nebo epluginu
    * @return string -- název akce
    */
   public static function getAjaxAction() {
      return self::$ajaxAction;
   }

   /**
    * Metoda vrací id item v ajaxu pro modul nebo eplugin
    * @return integer -- id volaného item
    */
   public static function getAjaxIdItem() {
      return self::$ajaxIdItem;
   }

   /**
    * Metoda vrací řetězec s parametry ajax souboru
    * @return string -- řetězec parametrů
    */
   public static function getAjaxFileParams() {
      return self::$ajaxParams;
   }

}
?>
