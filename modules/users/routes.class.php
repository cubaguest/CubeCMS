<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Users_Routes extends Routes {
	function initRoutes() {
//      $this->addRoute('adduser', "adduser", 'adduser', null);
//      $this->addRoute('addgroup', "addgroup", 'addgroup', null);
//      $this->addRoute('edituser', "user-::id::/edit/", 'edituser','user-{id}/edit');
//      $this->addRoute('editgroup', "group-::id::/edit/", 'editgroup','group-{id}/edit');

      $this->addRoute('usersList', "users.json", 'usersList','users.json');
      $this->addRoute('groupsList', "groups.json", 'groupsList','groups.json');

      $this->addRoute('editUser', 'edit-user.php', 'editUser', 'edit-user.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('blockUser', 'block-user.php', 'blockUser', 'block-user.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('editGroup', 'edit-group.php', 'editGroup', 'edit-group.php', 'XHR_Respond_VVEAPI');

      $this->addRoute('groups', "groups", 'groups','groups/');
	}
}

?>