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
      $this->addRoute('voteajax', 'vote.json', 'vote', 'vote.json');
      // editace ankety
      $this->addRoute('edit', "edit-(?P<id>[0-9]+)", 'edit','edit-{id}/');
      // zobrazení anket
      $this->addRoute('normal', null, 'main', null);
	}
}

?>