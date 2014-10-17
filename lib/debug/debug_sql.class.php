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
class Debug_Sql {
   /**
    * Časovač
    * @var <type>
    */
   private static $queries = array();

   public static function logQuery() {
      $args = func_get_args();
      foreach ($args as $key => $arg) {
         if(is_object($arg)){
            $args[$key] = clone $arg;
         }
      }
      self::$queries = array_merge(self::$queries, $args);
   }

   public static function printQueries(){
      if(!empty (self::$queries)){
         echo '<div class="debug-log">';// admin menu
         echo '<p><strong>DEBUG SQL OUTPUT:</strong></p>';
         $i = 1;
         foreach (self::$queries as $query) {
            echo '<h3>QUERY: '.$i.'</h3>';
            echo '<code>'.$query.'</code></br></br>';
            $i++;
         }
         echo '</div>';
      }
   }

}
