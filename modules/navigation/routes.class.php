<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Navigation_Routes extends Routes {
	function initRoutes()
   {
      $this->addRoute('editText', "edittext", 'editText', 'edittext/');
	}
}

?>