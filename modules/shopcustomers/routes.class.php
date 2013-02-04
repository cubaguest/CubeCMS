<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class ShopCustomers_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('customersGroups', "customers-groups/", 'customersGroups', 'customers-groups/');


      $this->addRoute('changeCustomer', 'customer.php', 'changeCustomer', 'customer.php', 'XHR_Respond_VVEAPI');
	}
}

?>