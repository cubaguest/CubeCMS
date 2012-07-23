<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class PressReports_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('add', "add/", 'add', 'add/');
      $this->addRoute('edit', "edit/id-(?P<id>[0-9]+)/", 'edit', 'edit/id-{id}/');
	}
}

?>