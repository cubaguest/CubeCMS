<?php
class Articles_Routes extends Routes {
   const FEEDS = true; // podle tohohle se generují feedy
   const FEED_FILE = '{type}.xml';

   function initRoutes() {
      $this->addRoute('top', "top", 'top', 'top/');
      $this->addRoute('archive', "archive", 'archive', 'archive/');

      
      $this->addRoute('add', "add", 'add', "add/");
      $this->addRoute('edit', "::urlkey::/edit", 'edit','{urlkey}/edit/');
      $this->addRoute('detail', "::urlkey::", 'show','{urlkey}/');
      // list starších článků v xml
      $this->addRoute('lastlist', "lastlist.(?P<output>(?:xml))", 'lastList', 'lastlist.{output}');
      $this->addRoute('current', "current.(?P<output>(?:xml))", 'currentArticle', 'current.{output}');
      // exporty
      $this->addRoute('exportFeed', "(?P<type>(?:rss)|(?:atom)).xml", 'exportFeed', self::FEED_FILE);
      $this->addRoute('detailExport', "::urlkey::\.(?P<output>(?:pdf)|(?:xml))", 'exportArticle','{urlkey}.{output}');


      $this->addRoute('normal', null, 'main', null);
	}
}

?>