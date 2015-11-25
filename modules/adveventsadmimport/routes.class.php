<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class AdvEventsAdmImport_Routes extends AdvEventsBase_Routes {
	function initRoutes()
   {
      parent::initRoutes();
      $this->addRoute('addSource');
      $this->addRoute('editSource', 'edit-source-::id::');
   }
}
