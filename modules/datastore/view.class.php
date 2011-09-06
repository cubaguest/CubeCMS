<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class DataStore_View extends View {
   public function mainView() 
   {
      if($this->category()->getRights()->isWritable()) {
         $this->controlls = true;
         $this->template()->addFile('tpl://main.phtml');
      } else {
         $this->template()->addFile('tpl://main-readonly.phtml');
      }
   }

   public function itemsListView()
   {
      $this->template()->addFile('tpl://list.phtml');

   }

   public function uploadFileView()
   {
   }     
}
?>