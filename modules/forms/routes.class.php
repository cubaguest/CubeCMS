<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Forms_Routes extends Routes {
   protected function initRoutes()
   {
      $this->addRoute('createForm', "create", 'createForm', "create/");
      $this->addRoute('editForm', "edit/::id::/", 'editForm','edit/{id}/');
      $this->addRoute('previewForm', "preview/::id::/", 'previewForm','preview/{id}/');
      
   }
   
}

?>