<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class AdminSites_Routes extends Routes {
	function initRoutes()
   {
      $this->addRoute('addSite', "add", 'addSite', "add/");
      $this->addRoute('addAlias', "add-alias", 'addAlias', "add-alias/");
      $this->addRoute('editSite', "edit-(?P<id>[0-9]+)/", 'editSite', 'edit-{id}/');
      $this->addRoute('editAlias', "edit-alias-(?P<id>[0-9]+)/", 'editAlias', 'edit-alias-{id}/');
   }
}
