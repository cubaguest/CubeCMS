<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class AdvEvents_Routes extends AdvEventsBase_Routes {
	function initRoutes()
   {
      parent::initRoutes();
      $this->addRoute('filter');
      $this->addRoute('detail', 'detail-::id::/', 'detail', 'detail-{id}/');
//      $this->addRoute('organisator', 'organisator-::id::/', 'organisator', 'organisator-{id}/');
      $this->addRoute('listAjax', 
          'events/(?P<year>[0-9]{4})/(?P<month>[0-1]?[0-9])/list.php', 
          'listAjax', 
          'events/{year}/{month}/list.php', 'XHR_Respond_VVEAPI'
          );
   }
}
