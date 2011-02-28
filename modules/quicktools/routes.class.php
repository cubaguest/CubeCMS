<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class QuickTools_Routes extends Routes {
	function initRoutes()
   {
      $this->addRoute('addTool', "add", 'addTool', 'add/');
      $this->addRoute('editTool', "edit-::id::", 'editTool', 'edit-{id}/');
	}
}

?>