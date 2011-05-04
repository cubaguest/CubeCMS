<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class DataStore_View extends View {
   public function mainView() {
      if($this->category()->getRights()->isWritable()) {
         $this->template()->addFile('tpl://main.phtml');
      } else {
      
      }
   }

   public function itemsListView()
   {
      $this->template()->addFile('tpl://list.phtml');

   }

}
?>