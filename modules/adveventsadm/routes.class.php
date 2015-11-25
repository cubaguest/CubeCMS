<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class AdvEventsAdm_Routes extends AdvEventsBase_Routes {
	function initRoutes()
   {
      parent::initRoutes();
      $this->addRoute('addEvent', 'add-event/', 'addEvent', 'add-event/');
      $this->addRoute('editEvent', 'edit-event-::idEvent::/', 'editEvent', 'edit-event-{idEvent}/');
      $this->addRoute('event', 'event-::idEvent::.html', 'event', 'event-{idEvent}.html');
   }
}
