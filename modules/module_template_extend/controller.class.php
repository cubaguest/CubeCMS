<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class MODULE_Controller extends MODULEEXTEND_Controller {

   public function init()
   {
       parent::init();
   }

   public function mainController()
   {
      parent::mainController();
   }

   public function settings(&$settings, Form &$form)
   {
      parent::settings($settings, $form);
   }
}
