<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class AdvEventsBase_Routes extends Routes {
	function initRoutes()
   {
      $this->addRoute('getPlaces', 'places.json', 'getPlaces', 'places.json', 'XHR_Respond_VVEAPI');
      $this->addRoute('getEvent', 'event.json', 'getEvent', 'event.json', 'XHR_Respond_VVEAPI');
   }
}
