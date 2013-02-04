<?php
class ShopCart_Panel extends Panel {
   private $cart = array();


   public function panelController() {
      $this->cart = new Shop_Cart();
      $this->cart->loadItems();
	}
	
	public function panelView() {
      
      $this->template()->cart = $this->cart;
      $this->template()->addFile('tpl://panel.phtml');
	}

//   public static function settingsController(&$settings,Form &$form) {
//   }
}
?>