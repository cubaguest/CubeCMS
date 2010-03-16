<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class PhpInfo_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('info', 'info.html', 'info', 'info.html');
      $this->addRoute('normal', null, 'main', null);
	}
}

?>