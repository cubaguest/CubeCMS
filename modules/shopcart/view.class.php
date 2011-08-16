<?php
class ShopCart_View extends View {
   public function mainView() 
   {
      $this->template()->addFile('tpl://cart.phtml');
   }
   
   public function orderView()
   {
      $this->template()->addFile('tpl://order.phtml');
   }
   
   public function orderCompleteView()
   {
      $this->template()->addFile('tpl://order-complete.phtml');
   }
}

?>