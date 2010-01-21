<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Users_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('adduser', "adduser", 'adduser', null);
      $this->addRoute('addgroup', "addgroup", 'addgroup', null);
      $this->addRoute('edituser', "user-::id::/edit/", 'edituser','user-{id}/edit');
      $this->addRoute('editgroup', "group-::id::/edit/", 'editgroup','group-{id}/edit');

      $this->addRoute('normal', null, 'main', null);
	}
}

?>