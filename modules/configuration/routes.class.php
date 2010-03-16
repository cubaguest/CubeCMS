<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Configuration_Routes extends Routes {
	function initRoutes() {
//      $this->addRoute('add', "add", 'add', null);
      $this->addRoute('edit', "option-::id::/edit/", 'edit','option-{id}/edit/');

      $this->addRoute('normal', null, 'main', null);
	}
}

?>