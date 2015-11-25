<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class AdvEventsAdmOrganizers_Routes extends AdvEventsBase_Routes {
	function initRoutes()
   {
      parent::initRoutes();
      $this->addRoute('addOrganizer', 'add-organizer/', 'addOrganizer', 'add-organizer/');
      $this->addRoute('editOrganizer', 'edit-organizer-::id::/', 'editOrganizer', 'edit-organizer-{id}/');
      $this->addRoute('detailOrganizer', 'detail-organizer-::id::/', 'detailOrganizer', 'detail-organizer-{id}/');
   }
}
