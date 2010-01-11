<?php
class Actionswgal_Controller extends Actions_Controller {

   public function showController(){
      parent::showController();
      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->setOption('idArt', $this->action->{Actions_Model_Detail::COLUMN_ID});
      $ctr->mainController();
   }

   public function editphotosController() {
      $this->checkWritebleRights();
      $actModel = new Actions_Model_Detail();
      $action = $actModel->getAction($this->getRequest('urlkey'));

      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->setOption('idArt', $action->{Actions_Model_Detail::COLUMN_ID});
      $ctr->editphotosController();
   }

   public function editphotoController() {
      $this->checkWritebleRights();
      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->editphotoController();
   }

   public function checkFileController() {
      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->checkFileController();
   }

   public function uploadFileController() {
      $this->checkWritebleRights();
      $actModel = new Actions_Model_Detail();
      $action = $actModel->getAction($this->getRequest('urlkey'));

      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());

      if($action !== false) {
         $ctr->setOption('idArt', $action->{Actions_Model_Detail::COLUMN_ID});
      }
      $ctr->uploadFileController();
   }
}
?>