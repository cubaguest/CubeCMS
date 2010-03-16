<?php
class Actionswgal_Controller extends Actions_Controller {

   public function showController(){
      parent::showController();
      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->setOption('idArt', $this->view()->action->{Actions_Model_Detail::COLUMN_ID});
      $ctr->setOption('subdir', $this->view()->action[Actions_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()].DIRECTORY_SEPARATOR);
      $ctr->mainController();
   }

   public function editphotosController() {
      $this->checkWritebleRights();
      $actModel = new Actions_Model_Detail();
      $action = $actModel->getAction($this->getRequest('urlkey'), $this->category()->getId());

      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->setOption('idArt', $action->{Actions_Model_Detail::COLUMN_ID});
      $ctr->setOption('subdir', $action[Actions_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()].DIRECTORY_SEPARATOR);
      $ctr->editphotosController();
      // odkaz zpět
      $this->view()->linkBack = $this->link()->back($this->link()->route(), 1);
   }

   protected function deleteAction($action) {
      // smazání galerie
      $photoCtrl = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $photoCtrl->setOption('idArt', $action->{Actions_Model_Detail::COLUMN_ID});
      $photoCtrl->setOption('subdir', $action[Actions_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()].DIRECTORY_SEPARATOR);
      $photoCtrl->deleteImages($action->{Actions_Model_Detail::COLUMN_ID});
      unset ($photoCtrl);
      // smazání akce
      $this->deleteActionData($action);
              
      $this->infoMsg()->addMessage(sprintf($this->_('Akce "%s" byla smazána', $this->getLocaleDomain()), $action->{Actions_Model_Detail::COLUMN_NAME}));
      $this->view()->linkBack->reload();
   }

   public function editphotoController() {
      $this->checkWritebleRights();
      $actModel = new Actions_Model_Detail();
      $action = $actModel->getAction($this->getRequest('urlkey'), $this->category()->getId());
      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->setOption('subdir', $action[Actions_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()].DIRECTORY_SEPARATOR);
      $ctr->editphotoController();
   }

   public function checkFileController() {
      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->checkFileController();
   }

   public function uploadFileController() {
      $this->checkWritebleRights();
      $actModel = new Actions_Model_Detail();
      $action = $actModel->getAction($this->getRequest('urlkey'),$this->category()->getId());
//      $action = $actModel->getActionById((int)$this->getRequestParam('addimage_idArt'));

      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());

      if($action !== false) {
         $ctr->setOption('idArt', $action->{Actions_Model_Detail::COLUMN_ID});
         $ctr->setOption('subdir', $action[Actions_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()].DIRECTORY_SEPARATOR);
      } else {
         return false;
      }
      $ctr->uploadFileController();
   }

   public static function settingsController(&$settings,Form &$form) {
      parent::settingsController($settings, $form);
      Photogalery_Controller::settingsController($settings, $form);
   }
}
?>