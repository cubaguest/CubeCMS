<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class NewsLetter_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('edittext', 'edittext', 'editText', 'edittext/');
      $this->addRoute('unregistration', 'unreg', 'unregistrationMail', 'unreg/');
      // registrace
      $this->addRoute('register', 'regmail.json', 'register', 'regmail.json');

      // ajax úpravy
      $this->addRoute('deleteMails', 'deleteMails.php', 'deleteMails', 'deleteMails.php', Routes::RESPOND_AJAX);

      $this->addRoute('sendMail', 'sendmail', 'sendMail', 'sendmail/');

      $this->addRoute('list', 'list', 'listMails', 'list/');
      $this->addRoute('export', 'list.(?P<output>(?:xml)|(?:csv)|(?:txt)|(?:json))', 'listMailsExport', 'list.{output}');
      $this->addRoute('normal', null, 'main', null);
	}
}

?>