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
   private static $vars = array();

   private static $tables = array();

   public static function log() {
      self::$vars = array_merge(self::$vars, func_get_args());
   }

   public static function printImmediately() {
      echo '<div class="debug-log" style="margin-top: 30px;">';// admin menu
      foreach (func_get_args() as $arg) {
         var_dump($arg);
      }
      echo '</div>';
      flush();
   }

   public static function table($array) {
      self::$vars = array_merge(self::$vars, func_get_args());
   }

   public static function printDebug(){
      if(!empty (self::$vars)){
         echo '<div class="debug-log">';// admin menu
         echo '<p><strong>DEBUG OUTPUT:</strong></p>';
         foreach (self::$vars as $arg) {
            var_dump($arg);
         }
         echo '</div>';
      }
      if(!empty (self::$tables)){
         echo '<div class="debug-log">';// admin menu
         echo '<p><strong>DEBUG TABLE OUTPUT:</strong></p>';
         foreach (self::$tables as $table) {
            echo '<table border="1" cellspacing="5" cellpadding="2">';
            foreach ($table as $row) {
               echo '<tr>';
               foreach ($row as $value) {
                  echo '<td>'.$value.'</td>';
               }
               echo '</tr>';
            }
            echo '</table>';
         }
         echo '</div>';
      }
   }
}
?>
