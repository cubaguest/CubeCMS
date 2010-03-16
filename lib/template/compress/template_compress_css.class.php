<?php
/**
 * Třída implementující kompresi css stylů
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE6.0.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		třída pro kompresi css
 */
class Template_Compress_CSS extends Template_Compress implements Template_Compress_Interface {
   /**
    * Metoda vrací zkomprimovaný string, připravený pro odeslání
    */
   public function pack(){
      $patterns = array('!/\*[^*]*\*+([^/][^*]*\*+)*/!','/[ \t\r\n]+/');
      $replacement = array('','');
      return preg_replace($patterns, $replacement, $this->string);
   }
}
?>
