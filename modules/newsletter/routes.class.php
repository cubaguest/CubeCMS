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
      $this->addRoute('deleteMails', 'deletemails', 'deleteMails', 'deleteMails/', 'XHR_Respond_VVEAPI');

      $this->addRoute('sendMail', 'sendmail', 'sendMail', 'sendmail/');

      $this->addRoute('list', 'list', 'listMails', 'list/');
      $this->addRoute('export', 'list.(?P<output>(?:xml)|(?:csv)|(?:txt)|(?:json))', 'listMailsExport', 'list.{output}');
      $this->addRoute('normal', null, 'main', null, 'XHR_Respond_VVEAPI');
	}
}

?>