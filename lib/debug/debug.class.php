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

   private static function prepareJsStringLiteral( $stringLiteral, $mode = 2 )
   {
      switch ( $mode )
      {
         case 2:
            $searches = array( '"', "\n" );
            $replacements = array( '\\"', "\\n\"\n\t+\"" );
            break;
         case 1:
            $searches = array( "'", "\n" );
            $replacements = array( "\\'", "\\n'\n\t+'" );
            break;
      }
      return str_replace( $searches, $replacements, $stringLiteral );
   }
   
   /**
    * Vypíše předané proměnné do popup js okna. Vhodné pro debug v šabloně.
    */
   public static function printVar() 
   {
      $args = func_get_args();
      echo '<script type="text/javascript">';
      echo '
         OpenWindow = window.open("", "Cube CMS Debugger", "height=800,width=800,modal=yes,scrollbars=yes");
         OpenWindow.document.write("<html>");
         OpenWindow.document.write("<head>");
         OpenWindow.document.write("<title>Cube CMS Debugger</title>");
         OpenWindow.document.write("</head>");
         OpenWindow.document.write("<body style=\"background-color: #FFCACB\">");
         OpenWindow.document.write("<h1 style=\"font-size: 110%;\">Cube CMS Debugger output</h1>");';
         
         $vardump = null;
         ob_start();
         foreach ($args as $arg) {
            var_dump($arg);
         }
         $cnt = ob_get_clean();
         $cnt = self::prepareJsStringLiteral($cnt);
//          $cnt = str_replace("\n", "" , $cnt);
         echo 'OpenWindow.document.write("'.$cnt.'");';
         echo 'OpenWindow.document.write("<br />");';
         
      echo 'OpenWindow.document.write("DEBUG END");
         OpenWindow.document.write("</body>");
         OpenWindow.document.write("</html>");';
      echo '</script>';
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
         ini_set('xdebug.var_display_max_data', 16384 );
         echo '<div class="debug-log">';// admin menu
         echo '<p><strong>DEBUG OUTPUT:</strong></p>';
         foreach (self::$vars as $arg) {
            if(function_exists('xdebug_var_dump')){
               xdebug_var_dump($arg);
            } else {
               var_dump($arg);
            }
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
