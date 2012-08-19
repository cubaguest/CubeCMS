<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class MailsAddressBook_Routes extends Routes {
	function initRoutes() {
      // nástroje
      $this->addRoute('tools', 'tools', 'tools', 'tools/');
      $this->addRoute('groups', 'groups', 'groups', 'groups/');
      
      $this->addRoute('addressList', 'address-list.json', 'addressList', 'address-list.json');
      $this->addRoute('groupsList', 'groups-list.json', 'groupsList', 'groups-list.json');
      
      $this->addRoute('editMail', 'edit-mail.php', 'editMail', 'edit-mail.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('editGroup', 'edit-group.php', 'editGroup', 'edit-group.php', 'XHR_Respond_VVEAPI');
	}
}

?>