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

   public static function table($array) {
      echo '<div class="debug-log" style="margin-top: 30px;">';// admin menu
      echo '<table border="1" cellspacing="5" cellpadding="2">';
      foreach ($array as $row) {
         echo '<tr>';
         foreach ($row as $value) {
            echo '<td>'.$value.'</td>';
         }
         echo '</tr>';
      }
      echo '</table>';
      echo '</div>';
      flush();
   }
}
?>
