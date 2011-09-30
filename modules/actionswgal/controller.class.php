<?php
class ActionsWGal_Controller extends Actions_Controller {

   protected function init()
   {
      parent::init();
      // registrace modulu fotogalerie pro obsluhu galerie
      $this->registerModule('photogalery');
   }
   
   public function showController(){
      parent::showController();
      if($this->view()->action == false) return false;
      
      // fotogalerie
      $this->view()->pCtrl = new Photogalery_Controller($this);
      $this->view()->pCtrl->loadText = false;
      $this->view()->pCtrl->idItem = $this->view()->action->{Actions_Model_Detail::COLUMN_ID};
      $this->view()->pCtrl->subDir = $this->view()->action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR;
      $this->view()->pCtrl->mainController();
      // adresáře k fotkám
      $this->view()->subdir = $this->view()->pCtrl->subDir;
      $this->view()->websubdir = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, $this->view()->pCtrl->subDir);
   }

   protected function deleteAction($action) {
      // smazání galerie
      $photoCtrl = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $photoCtrl->iditem = $action->{Actions_Model_Detail::COLUMN_ID};
      $photoCtrl->subDir = $action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR;
      $photoCtrl->deleteImages($action->{Actions_Model_Detail::COLUMN_ID});
      unset ($photoCtrl);
      // smazání akce
      $this->deleteActionData($action);
              
      $this->infoMsg()->addMessage(sprintf($this->tr('Akce "%s" byla smazána'), $action->{Actions_Model_Detail::COLUMN_NAME}));
      $this->view()->linkBack->reload();
   }

   /**
    * Metoda pro přípravu spuštění registrovaného modulu
    * @param Controller $ctrl -- kontroler modulu
    * @param string $module -- název modulu
    * @param string $action -- akce
    * @return type 
    */
   protected function callRegisteredModule(Controller $ctrl, $module, $action)
   {
      $model = new Actions_Model_Detail();
      $act = $model->getAction($this->getRequest('urlkey'), $this->category()->getId());
      
      if($act == false) return false;
      // base setup variables
      $ctrl->idItem = $act->{Actions_Model_Detail::COLUMN_ID};
      $ctrl->subDir = $act[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR;
      $ctrl->linkBack = $this->link()->route('detail');
      
      $ctrl->view()->name = $act->{Actions_Model_Detail::COLUMN_NAME};
   }
   
   protected function settings(&$settings,Form &$form) {
      parent::settings($settings, $form);
      $phCtrl = new Photogalery_Controller($this->category(), $this->routes(), $this->view(), $this->link());
      $phCtrl->settings($settings, $form);
   }
}
?>