<?php
/**
 * Třída pro výpisy a debug kódu
 *
 * @copyright  	Copyright (c) 2010 Jakub Matas
 * @version    	$Id: $ VVE 6.4 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu debug
 */
class Debug {
   
   public static function log() {
      if(VVE_DEBUG_LEVEL > 0){
         echo '<div class="debug-log" style="margin-top: 30px;">';// admin menu
         foreach (func_get_args() as $arg) {
            var_dump($arg);
         }
         echo '</div>';
         flush();
      }
      
   }
}
?>
