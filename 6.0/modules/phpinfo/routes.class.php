<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class PhpInfo_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('normal', null, 'main', null);
	}
}

?>