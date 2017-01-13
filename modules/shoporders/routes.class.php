<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class ShopOrders_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('ordersList', "orders-list.php", 'ordersList','orders-list.php');
      $this->addRoute('viewOrder', "view/::id::/", 'viewOrder','view/{id}/');
      $this->addRoute('deleteOrder', "delete.php", 'deleteOrder','delete.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('changeStatus', "change-status.php", 'changeStatus','change-status.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('exportOrder', "order-::id::\.(?P<output>(?:pdf))", 'exportOrder','order-{id}.{output}');
	}
}