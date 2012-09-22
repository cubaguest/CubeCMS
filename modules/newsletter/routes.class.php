<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class NewsLetter_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('edittext', 'edittext', 'editText', 'edittext/');
      $this->addRoute('unregistration', 'unreg', 'unregistrationMail', 'unreg/', 'XHR_Respond_VVEAPI');
      $this->addRoute('normal', null, 'main', null, 'XHR_Respond_VVEAPI');
	}
}

?>