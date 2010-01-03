<?php
class Actions_View extends View {
   public function init() {
      $this->template()->addCssFile("style.css");
   }


   public function mainView() {
      $this->template()->addTplFile("list.phtml");
   }

   public function showView(){
      $this->template()->addTplFile("detail.phtml");
   }

   public function archiveView(){
      $this->template()->addTplFile("archive.phtml");
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
      $this->template()->addTplFile('edit.phtml');
   }
}

?>