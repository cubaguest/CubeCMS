<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class NewsLetter_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('edittext', 'edittext', 'editText', 'edittext/');
      $this->addRoute('unregistration', 'unreg', 'unregistrationMail', 'unreg/', 'XHR_Respond_VVEAPI');
      // ajax úpravy
//      $this->addRoute('deleteMails', 'deletemails', 'deleteMails', 'deleteMails/', 'XHR_Respond_VVEAPI');

//      $this->addRoute('sendMail', 'sendmail', 'sendMail', 'sendmail/');

      $this->addRoute('listRegMails', 'list', 'listRegMails', 'list/');
      $this->addRoute('listMails', 'listMails.json', 'listMails', 'listMails.json');
      $this->addRoute('mailEdit', 'mailEdit.php', 'mailEdit', 'mailEdit.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('export', 'list.(?P<output>(?:xml)|(?:csv)|(?:txt)|(?:json))', 'listMailsExport', 'list.{output}');
      $this->addRoute('normal', null, 'main', null, 'XHR_Respond_VVEAPI');
	}
}

?>