<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
abstract class Shop_Product_Routes extends Routes {
	const URL_PARAM_SORT = 'sort';

   function initRoutes() {
      $this->addRoute('add', "add", 'add', "add/");
      $this->addRoute('editVariantsXHR', "::urlkey::/edit-variants.php", 'editVariants','{urlkey}/edit-variants.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('editVariants', "::urlkey::/edit-variants", 'editVariants','{urlkey}/edit-variants/');

//      $this->addRoute('saveProductCode', "::urlkey::/save-code", 'saveProductCode','{urlkey}/save-code/', 'XHR_Respond_VVEAPI');

      $this->addRoute('edit', "::urlkey::/edit", 'edit','{urlkey}/edit/');
      $this->addRoute('edittext', "edit-text/", 'edittext','edit-text/');
      $this->addRoute('detail', "::urlkey::", 'detail','{urlkey}/');

	}
}

?>