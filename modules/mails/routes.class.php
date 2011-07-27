<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Mails_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('addressList', 'address-list.json', 'addressList', 'address-list.json');
      $this->addRoute('groupsList', 'groups-list.json', 'groupsList', 'groups-list.json');
      // adresář
      $this->addRoute('addressBook', 'addressbook', 'addressBook', 'addressbook/', 'XHR_Respond_VVEAPI');
      // fronta odesílání
      $this->addRoute('sendMailsQueue', 'sendmails/queue', 'sendMailsQueue', 'sendmails/queue/');
      // seznam odeslaných emailů
      $this->addRoute('sendMailsList', 'sendmails', 'sendMailsList', 'sendmails/');
      
      $this->addRoute('editMail', 'edit-mail.php', 'editMail', 'edit-mail.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('editGroup', 'edit-group.php', 'editGroup', 'edit-group.php', 'XHR_Respond_VVEAPI');
      // ajax úpravy
      $this->addRoute('searchMail', 'searchmail.php', 'searchMail', 'searchmail.php', 'XHR_Respond_VVEAPI');
      // ajax odeslání
      $this->addRoute('sendMail', 'sendmail.php', 'sendMail', 'sendmail.php', 'XHR_Respond_VVEAPI');
	}
}

?>