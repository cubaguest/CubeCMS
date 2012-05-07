<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class ShopPrAttributes_View extends View {
   public function mainView()
   {
      $this->template()->addFile('tpl://main.phtml');
      Template_Module::setEdit(true);
   }
   
   public function attrGroupsListView()
   {
      echo json_encode($this->respond);
   }
   
   public function attrListView()
   {
      echo json_encode($this->respond);
   }
}

?>