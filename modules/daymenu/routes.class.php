<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class DayMenu_Routes extends Routes {
	function initRoutes() {
//      $this->addRoute('edit', "edit/(?P<date>(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d)/", 'edit', 'edit/{date}/');
//      $this->addRoute('edit', "edit/(?P<date>[0-9]{2}-[0-9]{2}-(19|20)\d\d)/", 'edit', 'edit/{date}/');
      // use $_GET['date']
      $this->addRoute('edit', "edit/", 'edit', 'edit/');
	}
}

?>