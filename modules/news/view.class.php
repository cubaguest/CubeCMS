<?php
class News_View extends Articles_View {
   public function mainView() {
      $this->template()->addTplFile("list.phtml");
////      $this->template()->addFile("tpl://list.phtml");
////      $this->template()->addFile("tpl://articles/list.phtml");
////      $this->template()->addFile("css://style.css");
////      $this->template()->addFile("js://functions.js");
   }

   public function showView() {
      $this->template()->addTplFile("detail.phtml", 'articles');
   }

   public function archiveView() {
      $this->template()->addTplFile("archive.phtml", 'articles');
   }

   /**
    * Viewer pro přidání článku
    */
   public function addView() {
      $this->template()->addTplFile("edit.phtml");
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->addView();
   }
}

?>
