<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class AdvEventsAdmCats_Routes extends AdvEventsBase_Routes {
	function initRoutes()
   {
      parent::initRoutes();
      $this->addRoute('addCategory', 'add-cat/', 'addCategory', 'add-cat/');
      $this->addRoute('editCategory', 'edit-cat-::id::/', 'editCategory', 'edit-cat-{id}/');
      $this->addRoute('detailCategory', 'detail-cat-::id::/', 'detailCategory', 'detail-cat-{id}/');
   }
}
