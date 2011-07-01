<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class DataTables_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('edit', "edit", 'edit', 'edit/');
	}
}

?>