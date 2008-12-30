<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class BlogRoutes extends Routes {
    const ROUTE_SECTIONS_ID = 1;

    function initRoutes() {
		$this->addRoute(self::ROUTE_SECTIONS_ID, 'sections', _('sekce'));
	}

    public function sectionsRoute(){
        return $this->getPredefRoute(self::ROUTE_SECTIONS_ID);
    }
}

?>