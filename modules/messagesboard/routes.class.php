<?php
class MessagesBoard_Routes extends Routes {
   function initRoutes() {
//      $this->addRoute('add', "add", 'add', "add/");
      $this->addRoute('edit', "edit/::id::/", 'edit','edit/{id}/');
      $this->addRoute('edittext', "edit-text/", 'edittext','edit-text/');
	}
}

?>