<?php
class Teams_Routes extends Routes {
   function initRoutes() {
      $this->addRoute('add', "add", 'add', "add/");
      $this->addRoute('edit', "edit/person-(?P<id>[0-9]+)/", 'edit', 'edit/person-{id}/');
      $this->addRoute('editOrder', "edit-order/", 'editOrder', 'edit-order/');
	}
}

?>