<?php
class Articles_View extends View {
   public function mainView() {
      $this->view()->template()->addTplFile("list.phtml");
      $this->view()->template()->addCssFile("style.css");
   }

   public function showView(){
   }

   /**
    * Viewer pro přidání článku
    */
   public function addView() {
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
   }
}

?>
