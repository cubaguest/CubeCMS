<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class CatsBulkEdit_Routes extends Routes {
	function initRoutes() {
//       $this->addRoute('edit', "edit", 'edit', 'edit/');

      // AJAX
//       $this->addRoute('changeIndPanels', "changepanels.php", 'changeIndPanels', 'changepanels.php', 'XHR_Respond_VVEAPI');
//       $this->addRoute('changeVisibility', "changevisibility.php", 'changeVisibility', 'changevisibility.php', 'XHR_Respond_VVEAPI');
//       $this->addRoute('moveCat', "movecat.php", 'moveCat', 'movecat.php', 'XHR_Respond_VVEAPI');
//       $this->addRoute('getCatInfo', "info.html", 'getCatInfo', 'info.html'); // info o kategorii

      $this->addRoute('edit', "cats-edit/", 'edit','cats-edit/');
	}
}

?>