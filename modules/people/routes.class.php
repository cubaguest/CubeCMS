<?php
class People_Routes extends Routes {
   function initRoutes() {
      $this->addRoute('add', "add", 'add', "add/");
      $this->addRoute('edit', "edit/people-(?P<id>[0-9]+)/", 'edit', 'edit/people-{id}/');
      $this->addRoute('editText');
      $this->addRoute('editOrder', "edit-order/", 'editOrder', 'edit-order/');
	}
}