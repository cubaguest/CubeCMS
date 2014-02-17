<?php
class FaceEdit_Routes extends Routes {
	function initRoutes() {
      
      $this->addRoute('saveFile', 'save-file.php', 'saveFile', 'save-file.php', 'XHR_Respond_VVEAPI');

      $this->addRoute('editFile', "edit-file", 'editFile','edit-file/');
	}
}