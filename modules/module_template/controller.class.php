<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class MODULE_Controller extends Controller {

   public function mainController()
   {}

   public function settings(&$settings, Form &$form)
   {}

   /* Autorun metody */
   public static function AutoRunDaily()
   {}
   public static function AutoRunHourly()
   {}
   public static function AutoRunMonthly()
   {}
   public static function AutoRunYearly()
   {}
   public static function AutoRunWeekly()
   {}
}
