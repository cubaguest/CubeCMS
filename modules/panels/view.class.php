<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Panels_View extends View {
   public function mainView() {
      $this->template()->addTplFile('list.phtml');
   }

   public function addView(){
      $this->template()->addTplFile('edit.phtml');
   }

   public function editView(){
      $this->addView();
      $this->edit=true;
   }

   public function getPanelsView() {
      print (json_encode($this->data));
   }

   public function getPanelInfoView() {
      print (json_encode($this->data));
   }
}

?>