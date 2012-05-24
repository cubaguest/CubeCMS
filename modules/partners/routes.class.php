<?php
class Partners_Routes extends Routes {
   function initRoutes() {
      $this->addRoute('add', "add", 'add', "add/");
      $this->addRoute('editOrder', "edit-order/", 'editOrder', 'edit-order/');
      $this->addRoute('edit', "edit/partner-(?P<id>[0-9]+)/", 'edit', 'edit/partner-{id}/');
	}
}

?>