<?php
class AdminHtaccess_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('edit', "edit/::id::/");
	}
}