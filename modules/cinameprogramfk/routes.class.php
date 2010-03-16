<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class CinameProgramFk_Routes extends CinemaProgram_Routes {
	function initRoutes() {
      parent::initRoutes();
      $this->addRoute('selYear', "(?P<year>20[0-9]{2})", 'main','{year}/');
	}
}

?>