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
//require_once('FirePHPCore/FirePHP.class.php');
class Debug {
   /**
    * Časovač
    * @var <type>
    */
   private static $times = 100;

   private static $vars = array();

   private static $tables = array();

   public static function log() {
      $args = func_get_args();
//      if(!class_exists('FirePHP')){
         self::$vars = array_merge(self::$vars, $args);
//      } else {
//         $firephp = FirePHP::getInstance(true);
//         foreach ($args as $arg) {
//            $firephp->log($arg);
//         }
//      }
   }

   public static function printImmediately() {
      echo '<div class="debug-log" style="margin-top: 30px;">';// admin menu
      $args = func_get_args();
      foreach ($args as $arg) {
         var_dump($arg);
      }
      echo '</div>';
      flush();
   }

   public static function table($array) {
      $args = func_get_args();
      if(!class_exists('FirePHP')){
         self::$vars = array_merge(self::$vars, $args);
      } else {
         $firephp = FirePHP::getInstance(true);
         foreach ($args as $key => $arg) {
            $firephp->table('DEBUG TABLE '.$key+1, $args);
         }
      }
   }

   public static function printDebug(){
      if(VVE_DEBUG_LEVEL > 0){
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

   public static function timerStart()
   {
      self::$times = microtime();
   }

   public static function timerStop($msg = null)
   {
      $t = microtime() - self::$times;
      array_push(self::$vars, 'Timer: '.$msg.' '.$t);
   }
}
?>
