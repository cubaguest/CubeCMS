<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class AdvEventsAdmPlaces_Routes extends AdvEventsBase_Routes {
	function initRoutes()
   {
      parent::initRoutes();
      $this->addRoute('addPlace', 'add-place/', 'addPlace', 'add-place/');
      $this->addRoute('editPlace', 'edit-place-::id::/', 'editPlace', 'edit-place-{id}/');
      $this->addRoute('detailPlace', 'detail-place-::id::/', 'detailPlace', 'detail-place-{id}/');
   }
}
