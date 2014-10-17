<?php
class AdminIPBlock_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('editIP', 'edit-ip.php', 'editIP', 'edit-ip.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('listIP', "iplist.json", 'listIP','iplist.json');
	}
}