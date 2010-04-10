<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Pokus_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('ajax', "ajax", 'ajax', 'ajax/', 'XHR_Respond_VVEAPI');
      $this->addRoute('messages', "messages", 'messages', 'messages/');
      $this->addRoute('normal', null, 'main', null);
	}
}

?>