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

      $this->addRoute('list', 'list', 'listMails', 'list/');
      $this->addRoute('export', 'list.(?P<output>(?:xml)|(?:csv)|(?:txt))', 'listMailsExport', 'list.{output}');
      $this->addRoute('normal', null, 'main', null);
	}
}

?>