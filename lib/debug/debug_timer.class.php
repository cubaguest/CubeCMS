<?php
/**
 * Třída pro výpisy a debug časového zpracování
 *
 * @copyright  	Copyright (c) 2010 Jakub Matas
 * @version    	$Id: $ VVE 7.4 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu časových razítek
 */
class Debug_Timer {
   /**
    * Časovač
    * @var <type>
    */
   private static $timers = array();

   /**
    * metoda pro vytvoření instance
    * @return Debug_Timer 
    */
   public static function getInstance() {
      return new self;
   }
   
   public static function printTimers() {
      foreach (self::$timers as $point => $timer) {
         foreach ($timer['times'] as $value) {
            echo '<div><strong>'.$point.' - '.$value['time'].'</strong> - '.$value['msg'].'</div>';
         }
      }
   }

   /**
    * Metoda spustí časovač
    * @param string $point
    * @return Debug_Timer 
    */
   public function timerStart($point)
   {
      self::$timers[$point]['start'] = microtime();
      return $this;
   }

   public static function timerStop($point, $msg = null)
   {
      $t = microtime() - self::$timers[$point]['start'];
      self::$timers[$point]['times'][] = array('time' => $t, 'msg' => $msg);
   }
}
?>
