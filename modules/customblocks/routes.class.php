<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class CustomBlocks_Routes extends Routes {
	function initRoutes()
   {
      $this->addRoute('addBlock', "add-block/::type::/", 'addBlock','add-block/{type}/');
      
      $this->addRoute('selectBlock', "add-block/", 'selectBlock','add-block/');
      
      $this->addRoute('editBlock', "edit-block/::id::/", 'editBlock','edit-block/{id}/');
      $this->addRoute('moveBlock', "move-block/::id::/", 'moveBlock','move-block/{id}/');
      $this->addRoute('sortBlocks', "sort-blocks/", 'sortBlocks','sort-blocks/');
      
      $this->addRoute('sortItems', "sort-items/::id::/", 'sortItems','sort-items/{id}/');
   }
}
