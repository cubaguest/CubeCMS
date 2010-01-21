<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Services_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('normal', null, 'main', null);
	}
}

?>