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
      $this->view()->pCtrl->idItem = $this->view()->action->{Actions_Model::COLUMN_ID};
      $this->view()->pCtrl->subDir = $this->view()->action[Actions_Model::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR;
      $this->view()->pCtrl->mainController();
      // adresáře k fotkám
      $this->view()->subdir = $this->view()->pCtrl->subDir;
      $this->view()->websubdir = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, $this->view()->pCtrl->subDir);
   }

   protected function deleteAction($action) {
      // smazání galerie
      $photoCtrl = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $photoCtrl->iditem = $action->{Actions_Model::COLUMN_ID};
      $photoCtrl->subDir = $action[Actions_Model::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR;
      $photoCtrl->deleteImages($action->{Actions_Model::COLUMN_ID});
      unset ($photoCtrl);
      // smazání akce
      $this->deleteActionData($action);
              
      $this->infoMsg()->addMessage(sprintf($this->tr('Akce "%s" byla smazána'), $action->{Actions_Model::COLUMN_NAME}));
      $this->link()->reload($this->view()->linkBack);
   }
   
   protected function moveEvent(Model_ORM_Record $event, $targetCatID)
   {
      parent::moveEvent($event, $targetCatID);
      $dir = new FS_Dir(self::getActionImgDir($event), $this->category()->getModule()->getDataDir());
      if($dir->exist()){
         $newCat = Category_Structure::getStructure(Category_Structure::ALL)->getCategory($targetCatID)->getCatObj();
         FS_Dir::checkStatic($newCat->getModule()->getDataDir());
         $newDir = $newCat->getModule()->getDataDir();
         $dir->move($newDir);
         
         $model = new PhotoGalery_Model_Images();
         $model
             ->where(PhotoGalery_Model_Images::COLUMN_ID_CAT." = :idc", array('idc' => $this->category()->getId()))
             ->update(array(PhotoGalery_Model_Images::COLUMN_ID_CAT => $newCat->getId()));
         
      }
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
      $model = new Actions_Model();
      $act = $model->where(Actions_Model::COLUMN_URLKEY." = :ukey && ".Actions_Model::COLUMN_ID_CAT." = :idc",
         array('ukey' => $this->getRequest('urlkey'), 'idc' => $this->category()->getId() ) )
         ->record();
      
      if($act == false) return false;
      // base setup variables
      $ctrl->idItem = $act->{Actions_Model::COLUMN_ID};
      $ctrl->subDir = $act[Actions_Model::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR;
      $ctrl->linkBack = $this->link()->route('detail');
      
      $ctrl->view()->name = $act->{Actions_Model::COLUMN_NAME};
   }
   
   protected function settings(&$settings,Form &$form) {
      parent::settings($settings, $form);
      $phCtrl = new Photogalery_Controller($this->category(), $this->routes(), $this->view(), $this->link());
      $phCtrl->settings($settings, $form);
   }
}
?>