<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Services_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('tablesList', "tablesList.json", 'tablesList','tablesList.json');

//      $this->addRoute('editUser', 'edit-user.php', 'editUser', 'edit-user.php', 'XHR_Respond_VVEAPI');
//      $this->addRoute('blockUser', 'block-user.php', 'blockUser', 'block-user.php', 'XHR_Respond_VVEAPI');
//      $this->addRoute('editGroup', 'edit-group.php', 'editGroup', 'edit-group.php', 'XHR_Respond_VVEAPI');

      $this->addRoute('fileAction', "fileact/", 'fileAction','fileact/');
      $this->addRoute('database', "database", 'database','database/');
      $this->addRoute('backup', "backup", 'backup','backup/');
	}
}

?>