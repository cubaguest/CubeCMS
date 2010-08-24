<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Mails_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('addressList', 'address-list.json', 'addressList', 'address-list.json');
      $this->addRoute('groupsList', 'groups-list.json', 'groupsList', 'groups-list.json');
      
      $this->addRoute('editMail', 'edit-mail.php', 'editMail', 'edit-mail.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('editGroup', 'edit-group.php', 'editGroup', 'edit-group.php', 'XHR_Respond_VVEAPI');
      // ajax úpravy
      $this->addRoute('sendMailsList', 'sendmails', 'sendMailsList', 'sendmails/');
      $this->addRoute('addressBook', 'addressbook', 'addressBook', 'addressbook/');

//      $this->addRoute('export', 'list.(?P<output>(?:xml)|(?:csv)|(?:txt)|(?:json))', 'listMailsExport', 'list.{output}');

      $this->addRoute('normal', null, 'main', null);
	}
}

?>