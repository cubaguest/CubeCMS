<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Configuration_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('edit', "option-::id::/edit/", 'edit','option-{id}/edit/');
	}
}

?>