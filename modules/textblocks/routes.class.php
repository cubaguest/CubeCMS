<?php
class TextBlocks_Routes extends Routes {
   function initRoutes() {
      $this->addRoute('add', "add", 'add', "add/");
      $this->addRoute('edit', "edit/block-(?P<id>[0-9]+)/", 'edit', 'edit/block-{id}/');
      $this->addRoute('editOrder', "edit-order/", 'editOrder', 'edit-order/');
	}
}