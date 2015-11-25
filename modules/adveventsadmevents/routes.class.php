<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class AdvEventsAdmEvents_Routes extends AdvEventsBase_Routes {
	function initRoutes()
   {
      parent::initRoutes();
      $this->addRoute('addEvent', 'add-event/', 'addEvent', 'add-event/');
      $this->addRoute('editEvent', 'edit-event-::id::/', 'editEvent', 'edit-event-{id}/');
      
      $this->addRoute('detailEvent', 'detail-event-::id::/', 'detailEvent', 'detail-event-{id}/');
      
      $this->addRoute('eventAction', 'event-::id::-action.php', 'eventAction', 'event-{id}-action.php', "XHR_Respond_VVEAPI");
   }
}
