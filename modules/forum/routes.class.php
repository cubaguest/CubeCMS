<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Forum_Routes extends Routes {
   protected function initRoutes() {
      $this->addRoute('addTopic', "add/", 'addTopic', "add/");

      $this->addRoute('addMessage', "(?P<id>[0-9]+)/add/", 'addMessage', "{id}/add/");
      $this->addRoute('editTopic', "(?P<id>[0-9]+)/edit/", 'editTopic', '{id}/edit/');
      $this->addRoute('editMessage', "(?P<id>[0-9]+)/(?P<idm>[0-9]+)/edit/", 'editMessage', '{id}/{idm}/edit/');
      $this->addRoute('cancelMessageNotify', "cancel-msg-notify/(?P<idm>[0-9]+)/", 'cancelMessageNotify', "cancel-msg-notify/{idm}/");
      $this->addRoute('showTopic', "(?P<id>[0-9]+)/", 'showTopic', '{id}/');
      $this->addRoute('rssTopic', "(?P<id>[0-9]+)/rss.xml", 'rssTopic', '{id}/rss.xml');
      $this->addRoute('voteMessage', "vote-message.json", 'voteMessage', 'vote-message.json', 'XHR_Respond_VVEAPI');
	}
}
