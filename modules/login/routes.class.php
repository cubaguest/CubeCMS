<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Login_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('changepasswd', 'changepasswd', 'changepasswd', 'changepasswd/');
      $this->addRoute('normal', null, 'main', null);
	}
}

?>