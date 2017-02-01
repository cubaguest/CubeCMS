<?php
class ShopCart_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('order', "objednavka/", 'order', 'objednavka/');
      $this->addRoute('orderComplete', "hotovo/", 'orderComplete', 'hotovo/');
      $this->addRoute('cartUpdate', 'cart.php', 'cartUpdate', 'cart.php', 'XHR_Respond_VVEAPI');  // GET parametr action určuje akci
	}
}