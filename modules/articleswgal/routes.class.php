<?php
class ArticlesWGal_Routes extends Articles_Routes {
	function initRoutes() {
      parent::initRoutes();
      $this->addRoute('editphoto', "::urlkey::/editphotos/editphoto-::id::", 'editphoto','{urlkey}/editphotos/editphoto-{id}/');
      $this->addRoute('editphotos', "::urlkey::/editphotos", 'editphotos','{urlkey}/editphotos/');

      $this->addRoute('uploadFile', "::urlkey::/uploadFile.php", 'uploadFile','{urlkey}/uploadFile.php');
      $this->addRoute('checkFile', "::urlkey::/checkFile.php", 'checkFile','{urlkey}/checkFile.php');
	}
}

?>