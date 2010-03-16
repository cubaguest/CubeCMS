<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class KzMainPage_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('edititems', "edititems", 'edititems', 'edititems/');
      $this->addRoute('editurls', "editurls", 'editurls', 'editurls/');
      $this->addRoute('loadArticles', "loadarts.php", 'loadArticles', 'loadarts.php');
      $this->addRoute('normal', null, 'main', null);
	}
}

?>