<?php
class Actionswgal_View extends Actions_View {
   public function init() {
      $this->template()->addCssFile("style.css");
   }


   public function mainView() {
      $this->template()->addTplFile("list.phtml", 'actions');
   }

   public function showView(){
      $this->template()->addTplFile("detail.phtml");
   }

   public function showPhotosView(){
//      $this->template()->addTplFile("listPhotos.phtml");
      $this->showView();
   }

   public function editphotosView(){
      $this->template()->addTplFile("addimage.phtml", 'photogalery');
      $this->template()->addTplFile("editphotos.phtml", 'photogalery');
   }

   public function editphotoView(){
      $this->template()->addTplFile("editphoto.phtml", 'photogalery');
   }

   public function uploadFileView() {}
   public function checkFileView() {}

   

   public function archiveView(){
      $this->template()->addTplFile("archive.phtml", 'actions');
   }

   /**
    * Viewer pro přidání novinky
    */
   public function addView() {
      $this->editView();
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->template()->addTplFile('edit.phtml', 'actions');
   }
}

?>