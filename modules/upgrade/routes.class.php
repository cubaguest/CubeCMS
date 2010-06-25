<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Upgrade_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('normal', null, 'main', null);
	}
}

?>