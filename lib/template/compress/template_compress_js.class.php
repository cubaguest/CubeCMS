<?php
/**
 * Třída implementující kompresi javascript za chodu
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE6.0.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		třída pro základní kompresi
 */
class Template_Compress_JS extends Template_Compress implements Template_Compress_Interface {
   /**
    * Metoda vrací zkomprimovaný string, připravený pro odeslání
    */
   public function pack(){
      $buffer = $this->string;
      // remove comments
      $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
      // remove tabs, spaces, newlines, etc.
      $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
      return $buffer;
   }
}
?>
