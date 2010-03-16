<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class GuestBook_Routes extends Routes {
   const FEEDS = true; // podle tohohle se generují feedy
   const FEED_FILE = '{type}.xml';
   
	function initRoutes() {
//      $this->addRoute('edit', "edit", 'edit', null);
//      $this->addRoute('edituu', "(?P<category>[a-z0-9_-]*)-(?P<idart>[0-9_-]*)/edit/", 'editPokusArt');
//      $this->addRoute('editui', "(?P<category>[a-z0-9_-]*)-(?P<idart>[0-9_-]*)/", 'showPokusArt');
//      $this->addRoute('edit2', "::article::/(?:page(?P<page>[0-9]*))?", 'main',
//                     '{article}/(page{page})');

      $this->addRoute('exportFeed', "(?P<type>(?:rss)|(?:atom)).xml", 'exportFeed', self::FEED_FILE);
      
      $this->addRoute('normal', null, 'main', null);
	}
}

?>