<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Text_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('edit', "edit", 'edit', 'edit/');
//      $this->addRoute('edituu', "(?P<category>[a-z0-9_-]*)-(?P<idart>[0-9_-]*)/edit/", 'editPokusArt');
//      $this->addRoute('editui', "(?P<category>[a-z0-9_-]*)-(?P<idart>[0-9_-]*)/", 'showPokusArt');
//      $this->addRoute('edit2', "::article::/(?:page(?P<page>[0-9]*))?", 'main',
//                     '{article}/(page{page})');

      $this->addRoute('normal', null, 'main', null);
	}
}

?>