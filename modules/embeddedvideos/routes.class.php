<?php

/**
 * Třída obsluhující cesty modulu
 *
 */
class EmbeddedVideos_Routes extends Routes {

   function initRoutes()
   {
      $this->addRoute('addVideo', "add-video/", 'addVideo', 'add-video/');
      $this->addRoute('editVideo', "edit-video/::id::/", 'editVideo', 'edit-video/{id}/');
//      $this->addRoute('sortBlocks', "sort-blocks/", 'sortBlocks', 'sort-blocks/');

      $this->addRoute('sortVideos', "sort-videos/", 'sortVideos', 'sort-videos/');
   }

}
