<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class TextWPhotos_Routes extends Text_Routes {
	function initRoutes() {
      parent::initRoutes();
      $this->registerModule('photogalery');
	}
}

?>