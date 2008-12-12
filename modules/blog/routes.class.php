<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class BlogRoutes extends Routes {
	function initRoutes() {
		$this->addRoute(1, 'sections', _('sekce'));
	}
}

?>