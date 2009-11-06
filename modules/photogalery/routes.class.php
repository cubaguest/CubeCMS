<?php
class Photogalery_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('editimages', "editimages", 'editimages', "editimages/");
      $this->addRoute('edittext', "edittext", 'edittext','edittext/');

      $this->addRoute('normal', null, 'main', null);
	}
}

?>