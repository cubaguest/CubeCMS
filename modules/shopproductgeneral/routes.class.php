<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class ShopProductGeneral_Routes extends Routes {
	const URL_PARAM_SORT = 'sort';

   function initRoutes() {
      $this->addRoute('add', "add", 'add', "add/");
      $this->addRoute('edit', "::urlkey::/edit", 'edit','{urlkey}/edit/');
      $this->addRoute('edittext', "edit-text/", 'edittext','edit-text/');
      $this->addRoute('detail', "::urlkey::", 'detail','{urlkey}/');
	}
}

?>