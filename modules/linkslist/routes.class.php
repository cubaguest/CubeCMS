<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class LinksList_Routes extends Routes {
	function initRoutes()
   {
      $this->addRoute('editText', "edittext", 'editText', 'edittext/');
      $this->addRoute('list');
      $this->addRoute('changePositon', null, null, null ,  "XHR_Respond_VVEAPI");
      $this->addRoute('add');
      $this->addRoute('edit', 'edit-::id::/');
	}
}