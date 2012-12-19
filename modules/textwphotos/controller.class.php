<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class TextWPhotos_Controller extends Text_Controller {
   
   protected function init()
   {
      parent::init();
      // registrace modulu fotogalerie pro obsluhu galerie
      $this->registerModule('photogalery');
   }

      /**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {
      parent::mainController();
      // fotogalerie
      $this->view()->pCtrl = new Photogalery_Controller($this);
      $this->view()->pCtrl->setOption('loadText', false);
      $this->view()->pCtrl->mainController();
   }

   public function settings(&$settings, Form &$form) {
      $phCtrl = new Photogalery_Controller($this);
      $phCtrl->settings($settings, $form);

      parent::settings($settings, $form);
   }
}

?>