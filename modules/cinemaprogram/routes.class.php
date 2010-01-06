<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class CinemaProgram_Routes extends Routes {
	function initRoutes() {
      // přidání filmu
      $this->addRoute('add', "add", 'add', null);
      // uprava
      $this->addRoute('edit', "edit-(?P<id>[0-9]{1,})", 'edit','edit-{id}/');
      //standard
      $this->addRoute('normaldate',  "(?P<day>[0-3]?[0-9]{1})/(?P<month>[0-1]?[0-9]{1})/(?P<year>[0-9]{4})", 'main','{day}/{month}/{year}/');
      $this->addRoute('normal', null, 'main', null);
	}
}

?>