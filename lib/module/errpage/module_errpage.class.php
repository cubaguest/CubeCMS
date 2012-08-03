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
   protected static $code = 404;

   public function runController() {
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'neznámá';
      switch ($this->getCode()) {
         case 403:
            Template_Output::addHeader("HTTP/1.0 403 Forbidden");
            if(VVE_DEBUG_LEVEL > 0){
               Log::msg('Neoprávněný přístup na stránku: '.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']." ze stránky: ".$referer, __CLASS__);
            }
            break;
         case 404:
         default:
            Template_Output::addHeader("HTTP/1.0 404 Not Found");
            if(VVE_DEBUG_LEVEL > 0){
               Log::msg('Nenalezení stránky: '.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].", přístup ze stránky: ".$referer, __CLASS__);
            }
            break;
      }
   }

   public function runView() {
      switch ($this->getCode()) {
         case 403:
            Template_Core::setPageTitle($this->tr('K požadované stránce nemáte přístup'));
            Template_Output::addHeader("HTTP/1.0 403 Forbidden");
            $this->template()->addTplFile('error/403.phtml');
            break;
         case 404:
         default:
            Template_Core::setPageTitle($this->tr('Chyba: stránka nenalezena'));
            Template_Output::addHeader("HTTP/1.0 404 Not Found");
            $this->template()->addTplFile('error/404.phtml');
            break;
      }
   }

   public function runTxtView() {
      switch ($this->getCode()) {
         case 403:
            echo $this->tr('CHYBA: K požadované stránce nemáte přístup');
            break;
         case 404:
         default :
            echo $this->tr('CHYBA: Stránka nenalezena');
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
