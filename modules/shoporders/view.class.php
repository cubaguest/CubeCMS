<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class ShopOrders_View extends View {
   public function mainView() {
      $this->template()->addFile('tpl://list.phtml');
      Template_Module::setEdit(true);
   }
   
   public function ordersListView(){
      echo json_encode($this->respond);
   }

   public function viewOrderView()
   {
      $this->template()->addFile('tpl://detail.phtml');
   }
}

?>