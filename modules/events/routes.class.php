<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Events_Routes extends Routes {
	function initRoutes() {

      $this->addRoute('addCat', "add-cat/", 'addCat','add-cat/');
      $this->addRoute('editCat', "edit-cat/::idcat::/", 'editCat','edit-cat/{idcat}/');
      $this->addRoute('listCats', "ev-list-cats/", 'listCats','ev-list-cats/');
      
      
      $this->addRoute('addEvent', "add-event/", 'addEvent','add-event/');
      $this->addRoute('editEvent', "edit-event/::idevent::/", 'editEvent','edit-event/{idevent}/');
      $this->addRoute('listEvents', "ev-list/", 'listEvents','ev-list/');
      
      $this->addRoute('listEventsPublicAdd', "ev-list-public-add/", 'listEventsPublicAdd','ev-list-public-add/');
      
      
      $this->addRoute('exports', "exports/", 'exports','exports/');
      
      
	}
}

?>