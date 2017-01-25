<?php
class Partners_Routes extends Routes {
   function initRoutes() {
      $this->addRoute('add');
      $this->addRoute('addGroup');
      $this->addRoute('editOrder', "order/group-(?P<id>[0-9]+)/", 'editOrder', 'order/group-{id}/');
      $this->addRoute('editGroupsOrder');
      $this->addRoute('editText', "edit-text/", 'editText', 'edit-text/');
      $this->addRoute('edit', "edit/partner-(?P<id>[0-9]+)/", 'edit', 'edit/partner-{id}/');
      $this->addRoute('editGroup', "edit/group-(?P<id>[0-9]+)/", 'editGroup', 'edit/group-{id}/');
	}
}