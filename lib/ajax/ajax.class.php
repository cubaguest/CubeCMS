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
    * Název prováděné akce
    * @var string
    */
   private $action = null;

   /**
    * Asoc. pole s parametry skriptu
    * @var array
    */
   private $paramsArray = array();
   /**
    * Konstruktor pro vytvoření objektu pro práci s ajaxem
    * @param string $fileParams -- řetězec parametrů předané scriptu
    */
   public function  __construct($fileParams) {
      $this->action = Ajax_Link::AJAX_DEFAULT_ACTION;
      // parametry přenesené přes GET
      if(!empty ($fileParams)){
         $params = explode('&', $fileParams);
         $par = array();
         foreach ($params as $variable) {
            $p = explode('=', $variable);
            $par[$p[0]] = rawurldecode($p[1]);
         }

         if(isset ($par[Ajax_Link::AJAX_ACTION_INDEX])){
            $this->action = $par[Ajax_Link::AJAX_ACTION_INDEX];
            unset ($par[Ajax_Link::AJAX_ACTION_INDEX]);
         }
         $this->paramsArray = $par;
      }
      //      Pokud je odesláno metodou post
      if(!empty ($_POST)){
         if(isset ($_POST[Ajax_Link::AJAX_ACTION_INDEX])){
            $this->action = addslashes($_POST[Ajax_Link::AJAX_ACTION_INDEX]);
         }
      }
   }

   /**
    * Metoda vrací parametr ajax souboru
    * @param string $name -- název parametru
    * @param mixed $defaultVaalue -- výchozí hodnota
    * @return mixed -- hodnota parametru nebo výchozí hodnota
    */
   public function getAjaxParam($name, $defaultVaalue = null) {
      if(isset($_POST[$name])){
         return addslashes($_POST[$name]);
      } else if(isset ($this->paramsArray[$name])){
         return $this->paramsArray[$name];
      } else {
         return $defaultVaalue;
      }
   }

   /**
    * Metoda vrací název akce ajaxu
    * @return string -- název akce (metody, která se  má provést)
    */
   public function getAjaxMetod() {
      return $this->action.self::AJAX_METHOD_SUFIX;
   }
}
?>
