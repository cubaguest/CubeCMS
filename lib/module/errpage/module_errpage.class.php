<?php
/**
 * Třída Core Modulu pro obsluhu chybové stránky
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 7.18 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro obsluhu chybové stránky
 */
class Module_ErrPage extends Module_Core {
   protected $code = 404;

   public function runController() {
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'neznámá';
      switch ($this->getCode()) {
         case 403:
            Template_Output::addHeader("HTTP/1.0 403 Forbidden");
            if(VVE_DEBUG_LEVEL > 0){
               Log::msg('(403): Neoprávněný přístup na stránku: '.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']." ze stránky: ".$referer, __CLASS__);
            }
            break;
         case 401:
            Template_Output::addHeader("HTTP/1.0 403 Forbidden");
            if(VVE_DEBUG_LEVEL > 0){
               Log::msg('(401): Neoprávněný přístup na stránku: '.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']." ze stránky: ".$referer, __CLASS__);
            }
            break;
         case 404:
         default:
            Template_Output::addHeader("HTTP/1.0 404 Not Found");
            if(VVE_DEBUG_LEVEL > 0){
               Log::msg('(404): Nenalezení stránky: '.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].", přístup ze stránky: ".$referer, __CLASS__);
            }
            break;
      }
   }

   public function runView() {
      switch ($this->getCode()) {
         case 403:
            Template_Core::setPageTitle($this->tr('K požadované stránce je přístup zakázán'));
            Template_Output::addHeader("HTTP/1.0 403 Forbidden");
            $this->template()->addTplFile('error/403.phtml');
            break;
         case 403:
            Template_Core::setPageTitle($this->tr('K požadované stránce nemáte nemáte s Vašimi právy přístup'));
            Template_Output::addHeader("HTTP/1.0 401 Unauthorized");
            $this->template()->addTplFile('error/401.phtml');
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
            echo $this->tr('CHYBA(403): K požadované stránce je přístup zakázán');
            break;
         case 403:
            echo $this->tr('CHYBA(401): K požadované stránce nemáte s Vašimi právy přístup');
            break;
         case 404:
         default :
            echo $this->tr('CHYBA(404): Stránka nenalezena');
            break;
      }
   }

   /**
    * Metoda vrací chybový kód
    * @return int
    */
   protected function getCode() {
      return $this->code;
   }

   /**
    * Metoda nasatvuje chybový kód
    * @param int $code -- např. 404, atd
    */
   public function setCode($code) {
      $this->code = $code;
   }
}
