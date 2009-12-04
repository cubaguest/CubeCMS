<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class CinemaProgram_Routes extends Routes {
	function initRoutes() {
      // přidání filmu
      $this->addRoute('add', "add", 'add', null);
      //standard
      $this->addRoute('normal', null, 'main', null);
	}
}

?>