<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Forum_Routes extends Routes {
   protected function initRoutes() {
      $this->addRoute('addTopic', "add/", 'addTopic', "add/");

      $this->addRoute('addPost', "(?P<id>[0-9]+)/add/", 'addPost', "{id}/add/");
      $this->addRoute('editTopic', "(?P<id>[0-9]+)/edit/", 'editTopic', '{id}/edit/');
      $this->addRoute('editPost', "(?P<id>[0-9]+)/(?P<idp>[0-9]+)/edit/", 'editPost', '{id}/{idp}/edit/');
      $this->addRoute('showTopic', "(?P<id>[0-9]+)/", 'showTopic', '{id}/');
      $this->addRoute('rssTopic', "(?P<id>[0-9]+)/rss.xml", 'rssTopic', '{id}/rss.xml');
      
	}
}
?>