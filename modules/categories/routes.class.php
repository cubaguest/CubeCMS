<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Categories_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('add', "add", 'add', null);
      $this->addRoute('addSection', "addsection", 'addsection', null);
      $this->addRoute('connectCat', "connect", 'connect', null);
//      $this->addRoute('edituu', "(?P<category>[a-z0-9_-]*)-(?P<idart>[0-9_-]*)/edit/", 'editPokusArt');
//      $this->addRoute('editui', "(?P<category>[a-z0-9_-]*)-(?P<idart>[0-9_-]*)/", 'showPokusArt');
      $this->addRoute('edit', "category-::categoryid::/edit/", 'edit','category-{categoryid}/edit');
      $this->addRoute('editSec', "section-::secid::/edit/", 'editSection','section-{secid}/edit');
      $this->addRoute('detail', "category-::categoryid::/", 'show','category-{categoryid}');

      $this->addRoute('normal', null, 'main', null);
	}
}

?>