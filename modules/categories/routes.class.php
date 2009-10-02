<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Categories_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('add', "add", 'add', null);
//      $this->addRoute('edituu', "(?P<category>[a-z0-9_-]*)-(?P<idart>[0-9_-]*)/edit/", 'editPokusArt');
//      $this->addRoute('editui', "(?P<category>[a-z0-9_-]*)-(?P<idart>[0-9_-]*)/", 'showPokusArt');
      $this->addRoute('edit', "::category::/edit/", 'edit','{category}/edit');
      $this->addRoute('detail', "::category::/", 'show','{category}');

      $this->addRoute('normal', null, 'main', null);
	}
}

?>