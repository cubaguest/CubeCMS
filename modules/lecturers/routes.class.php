<?php
class Lecturers_Routes extends Routes {
   function initRoutes() {
      $this->addRoute('add', "add", 'add', "add/");
      
      $this->addRoute('edit', "edit/lecturer-(?P<id>[0-9]+)/", 'edit', 'edit/lecturer-{id}/');

      $this->addRoute('normal', null, 'main', null);
	}
}

?>