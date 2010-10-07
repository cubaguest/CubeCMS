<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Login_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('changePasswd', 'changepasswd', 'changePasswd', 'changepasswd/');
      $this->addRoute('changeUser', 'changeuser', 'changeUser', 'changeuser/');
      $this->addRoute('newPassword', 'newpassword', 'newPassword', 'newpassword/');
      $this->addRoute('normal', null, 'main', null);
	}
}

?>