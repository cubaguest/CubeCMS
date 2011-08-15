<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class DayMenu_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('edit', "edit/(?P<day>[1-7])/", 'edit', 'edit/{day}/');
	}
}

?>