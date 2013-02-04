<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class ShopSettings_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('currencyAndTaxes', "currencyandtaxes/", 'currencyAndTaxes', 'currencyandtaxes/');
      $this->addRoute('orders', "orders/", 'orders','orders/');
      $this->addRoute('shipAndPay', "shipandpay/", 'shipAndPay','shipandpay/');
      $this->addRoute('customers', "customers/", 'customers','customers/');
      $this->addRoute('taxesList', "taxes.json", 'taxesList','taxes.json');
      $this->addRoute('editTax', 'edit-tax.php', 'editTax', 'edit-tax.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('paymentsList', "payments.json", 'paymentsList','payments.json');
      $this->addRoute('editPayment', 'edit-payment.php', 'editPayment', 'edit-payment.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('shippingsList', "shippings.json", 'shippingsList','shippings.json');
      $this->addRoute('editShipping', 'edit-shipping.php', 'editShipping', 'edit-shipping.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('mailVariables', 'mail-vars.json', 'mailVariables', 'mail-vars.json', 'XHR_Respond_VVEAPI');
	}
}

?>