<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class ShopProductGeneralAdmin_Routes extends Shop_Product_Routes {

   function initRoutes() {
      parent::initRoutes();

      $this->addRoute('edit', "product-(?P<idp>[0-9]+)/edit/", 'edit','product-{idp}/edit/');
      $this->addRoute('editVariantsXHR', "product-(?P<idp>[0-9]+)/edit-variants.php", 'editVariants','product-{idp}/edit-variants.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('editVariants', "product-(?P<idp>[0-9]+)/edit-variants/", 'editVariants','product-{idp}/edit-variants/');
      $this->addRoute('detail', "product-(?P<idp>[0-9]+)/", 'detail','product-{idp}/');
	}
}
