<?php
class Photogalerymed_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('add', "add", 'add', "add/");
      $this->addRoute('edittext', "::urlkey::/edittext", 'edittext','{urlkey}/edittext/');
      $this->addRoute('editphoto', "::urlkey::/editimages/editphoto-::id::", 'editphoto','{urlkey}/editimages/editphoto-{id}/');
      $this->addRoute('editimages', "::urlkey::/editimages", 'editimages','{urlkey}/editimages/');

      $this->addRoute('detail', "::urlkey::", 'show','{urlkey}/');

      $this->addRoute('normal', null, 'main', null);
	}
}

?>