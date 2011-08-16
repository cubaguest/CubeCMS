<?php
class ShopCart_Panel extends Panel {
   private $basket = array();


   public function panelController() {
      $this->basket = new Shop_Basket();
      $this->basket->loadItems();
	}
	
	public function panelView() {
      
      $this->template()->basket = $this->basket;
      $this->template()->addFile('tpl://panel.phtml');
	}

//   public static function settingsController(&$settings,Form &$form) {
//   }
}
?>