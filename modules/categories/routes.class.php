<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Categories_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('add', "add", 'add', 'add/');
      $this->addRoute('moduleDoc', "moduledoc.html", 'moduleDoc', 'moduledoc.html');

      // AJAX
      $this->addRoute('changeIndPanels', "changepanels.php", 'changeIndPanels', 'changepanels.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('changeVisibility', "changevisibility.php", 'changeVisibility', 'changevisibility.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('moveCat', "movecat.php", 'moveCat', 'movecat.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('getCatInfo', "info.html", 'getCatInfo', 'info.html'); // info o kategorii

      $this->addRoute('edit', "category-::categoryid::/edit/", 'edit','category-{categoryid}/edit/');
      $this->addRoute('settings', "category-::categoryid::/settings/", 'catSettings','category-{categoryid}/settings/');
      $this->addRoute('detail', "category-::categoryid::/", 'show','category-{categoryid}/');
      $this->addRoute('adminMenu', "adminmenu/", 'adminMenu','adminmenu/');

      $this->addRoute('normal', null, 'main', null);
	}
}

?>