<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Empty_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('normal', null, 'main', null);
	}
}

?>