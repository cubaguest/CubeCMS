<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Mails_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('addressList', 'address-list.json', 'addressList', 'address-list.json');
//      $this->addRoute('addMail', 'add-mail', 'addMail', 'add-mail/');
      $this->addRoute('editMail', 'edit-mail.php', 'editMail', 'edit-mail.php', 'XHR_Respond_VVEAPI');
      // ajax úpravy
//      $this->addRoute('deleteMails', 'deletemails', 'deleteMails', 'deleteMails/', 'XHR_Respond_VVEAPI');

//      $this->addRoute('composeMail', 'compose', 'composeMail', 'compose/');
      $this->addRoute('sendMailsList', 'sendmails', 'sendMailsList', 'sendmails/');
      $this->addRoute('addressBook', 'addressbook', 'addressBook', 'addressbook/');

//      $this->addRoute('list', 'list', 'listMails', 'list/');
//      $this->addRoute('export', 'list.(?P<output>(?:xml)|(?:csv)|(?:txt)|(?:json))', 'listMailsExport', 'list.{output}');
      


      $this->addRoute('normal', null, 'main', null);
	}
}

?>