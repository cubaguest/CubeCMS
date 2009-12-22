<?php
class Photogalery_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('editphoto', "editimages/editphoto-::id::", 'editphoto', "editimages/editphoto-{id}/");
      
      $this->addRoute('editimages', "editimages", 'editimages', "editimages/");
      $this->addRoute('edittext', "edittext", 'edittext','edittext/');

      $this->addRoute('detail', null, 'main', null);
	}
}

?>