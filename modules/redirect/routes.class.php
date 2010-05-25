<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Redirect_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('normal', null, 'main', null);
	}
}

?>