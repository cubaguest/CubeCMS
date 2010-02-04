<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Categories_View extends View {
   public function mainView() {
      $this->template()->addTplFile('list.phtml');
      $this->template()->addCssFile('style.css');
   }

   public function showView() {
      $this->template()->addTplFile('detail.phtml');
   }

   public function editView(){
      $this->template()->addTplFile('edit.phtml');
   }

   public function addView(){
      $this->editView();
   }

   public function moduleDocView() {
      print ($this->doc);
   }
}

?>