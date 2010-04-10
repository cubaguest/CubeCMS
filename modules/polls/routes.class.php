<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Polls_Routes extends Routes {
	function initRoutes() {
      // přidání ankety
      $this->addRoute('add', 'add', 'add', 'add/');
      // hlasování
      $this->addRoute('pollData', 'poll.php', 'pollData', 'poll.php');
      // editace ankety
      $this->addRoute('edit', "edit-(?P<id>[0-9]+)", 'edit','edit-{id}/');
      // zobrazení anket
      $this->addRoute('normal', null, 'main', null, 'XHR_Respond_VVEAPI');
	}
}

?>