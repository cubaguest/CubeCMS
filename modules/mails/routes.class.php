<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Mails_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('addMail', 'add-mail', 'addMail', 'add-mail/');
      $this->addRoute('editMail', 'edit-mail-(?P<id>[0-9]+)', 'editMail', 'edit-mail-{id}/');
      // ajax úpravy
      $this->addRoute('deleteMails', 'deletemails', 'deleteMails', 'deleteMails/', 'XHR_Respond_VVEAPI');

      $this->addRoute('composeMail', 'compose', 'composeMail', 'compose/');

      $this->addRoute('list', 'list', 'listMails', 'list/');
      $this->addRoute('export', 'list.(?P<output>(?:xml)|(?:csv)|(?:txt)|(?:json))', 'listMailsExport', 'list.{output}');
      $this->addRoute('normal', null, 'main', null, 'XHR_Respond_VVEAPI');
	}
}

?>