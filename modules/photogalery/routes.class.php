<?php
class Photogalery_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('editphoto', "editphotos/editphoto-::id::", 'editphoto', "editphotos/editphoto-{id}/");
      
      $this->addRoute('editphotos', "editphotos", 'editphotos', "editphotos/");
      $this->addRoute('edittext', "edittext", 'edittext','edittext/');

      $this->addRoute('uploadFile', "uploadFile.php", 'uploadFile','uploadFile.php', "XHR_Respond_VVEAPI");
      $this->addRoute('checkFile', "checkFile.php", 'checkFile','checkFile.php', "XHR_Respond_VVEAPI");

      $this->addRoute('detail', null, 'main', null);
	}
}

?>