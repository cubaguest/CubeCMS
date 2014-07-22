<?php
/**
 * Třída pro výpisy a debug časového zpracování
 *
 * @copyright     Copyright (c) 2010 Jakub Matas
 * @version       $Id: $ VVE 7.4 $Revision: $
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

   private $point = null;
   
   public function __construct($point, $startImmediately = true) 
   {
      $this->point = $point;
      if($startImmediately){
         $this->start();
      }
   } 
   
   /**
    * metoda pro vytvoření instance
    * @return Debug_Timer 
    */
   public static function getInstance() {
      $debug = debug_backtrace();
      $point = pathinfo($debug[0]['file'], PATHINFO_FILENAME)." - line: ".$debug[0]['line'];
      return new self($point);
   }
   
   public static function printTimers() {
      foreach (self::$timers as $point => $timer) {
         foreach ($timer['times'] as $value) {
            echo '<div><strong>'.$point.' - '. ($value['time'] > 0.0001 ? $value['time'] : 0) .'</strong> - '.$value['msg'].'</div>';
         }
      }
   }

   /**
    * Metoda spustí časovač
    * @param string $point
    * @return Debug_Timer 
    */
   public function timerStart($point = null)
   {
      self::$timers[$point == null ? $this->point : $point]['start'] = microtime(true);
      return $this;
   }

   public static function timerStop($point, $msg = null)
   {
      $t = microtime(true) - self::$timers[$point]['start'];
      self::$timers[$point]['times'][] = array('time' => $t, 'msg' => $msg);
   }
   
   public function start()
   {
      self::$timers[$this->point]['start'] = microtime(true);
      self::$timers[$this->point]['times'][] = array('time' => 0, 'msg' => 'TIMER START');
      return $this;
   }
   
   public function stop($msg = null)
   {
      $t = microtime(true) - self::$timers[$this->point]['start'];
      self::$timers[$this->point]['times'][] = array('time' => $t, 'msg' => $msg);
      return $this;
   }
   
   public function getTime($point = null) 
   {
      $point = $point == null ? $this->point : $point;
      if(isset(self::$timers[$point])){
         return self::$timers[$point]['times'];
      }
      return -1;
   }
}
?>
