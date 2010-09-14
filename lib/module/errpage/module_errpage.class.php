<?php
/**
 * Třída Core Modulu pro obsluhu chybové stránky
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 6.2 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro obsluhu chybové stránky
 */
class Module_ErrPage extends Module_Core {
   private static $code = 404;

   public function runController($type) {
      switch ($this->getCode()) {
         case 404:
         default:
            Template_Output::addHeader("HTTP/1.0 404 Not Found");
            break;
      }

      Log::msg('Nenalezení stránky: '.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'], __CLASS__);
   }

   public function runView() {
      switch ($this->getCode()) {
         case 404:
         default:
            Template_Output::addHeader("HTTP/1.0 404 Not Found");
            $this->template()->addTplFile('error/404.phtml');
            break;
      }
   }

   public function runTxtView() {
      switch ($this->getCode()) {
         case 404:
         default :
            echo _('CHYBA: Stránka nenalezena');
            break;
      }
   }

   /**
    * Metoda vrací chybový kód
    * @return int
    */
   private function getCode() {
      return self::$code;
   }

   /**
    * Metoda nasatvuje chybový kód
    * @param int $code -- např. 404, atd
    */
   public static function setCode($code) {
      self::$code = $code;
   }
}

?>
