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
      $this->addRoute('editImages', "::urlkey::/edit-images/", 'editimages','{urlkey}/edit-images/');
      $this->addRoute('editImageXHR', "::urlkey::/edit-image.php", 'editImageXHR','{urlkey}/edit-image.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('edit', "::urlkey::/edit", 'edit','{urlkey}/edit/');
      // detail a editace úvodního textu (proč text?)
      $this->addRoute('edittext', "edit-text/", 'edittext','edit-text/');
      $this->addRoute('detail', "::urlkey::", 'detail','{urlkey}/');

	}
}
