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

      $this->addRoute('detailpdf', "::urlkey::.pdf", 'showPdf','{urlkey}.pdf');

      $this->addRoute('export', "(?P<type>(?:rss)|(?:atom)).xml", 'export', self::FEED_FILE);
      $this->addRoute('normal', null, 'main', null);
	}
}

?>