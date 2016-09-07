<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class TextStatic_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('edit', "edit", 'edit', 'edit/');
	}
}