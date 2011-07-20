<?php
class MapLocations_Routes extends Routes {

   function initRoutes() {
      $this->addRoute('list', "list", 'list', "list/"); 
      $this->addRoute('add', "add", 'add', "add/");
      $this->addRoute('edit', "edit/::id::/", 'edit','edit/{id}/');
   }
}

?>