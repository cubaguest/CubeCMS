<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Categories_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('add', "add", 'add', null);
      $this->addRoute('moduleDoc', "moduledoc.html", 'moduleDoc', 'moduledoc.html');
      $this->addRoute('edit', "category-::categoryid::/edit/", 'edit','category-{categoryid}/edit');
      $this->addRoute('detail', "category-::categoryid::/", 'show','category-{categoryid}');

      $this->addRoute('normal', null, 'main', null);
	}
}

?>