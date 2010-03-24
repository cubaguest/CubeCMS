<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Text_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('edit', "edit", 'edit', 'edit/');
      $this->addRoute('editpanel', "editpanel", 'editPanel', 'editpanel/');
      $this->addRoute('normal', null, 'main', null);
	}
}

?>