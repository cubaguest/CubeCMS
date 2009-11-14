<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Users_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('add', "add", 'add', null);
      $this->addRoute('edit', "user-::id::/edit/", 'edit','user-{id}/edit');

      $this->addRoute('normal', null, 'main', null);
	}
}

?>