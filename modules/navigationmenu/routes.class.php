<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class NavigationMenu_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('add', "add", 'add', null);
      $this->addRoute('edit', "item-::id::/edit/", 'edit','item-{id}/edit');

      $this->addRoute('normal', null, 'main', null);
	}
}

?>