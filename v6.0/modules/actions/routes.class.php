<?php
class Actions_Routes extends Routes {
   const FEEDS = true; // podle tohohle se generují feedy
   const FEED_FILE = '{type}.xml';

   function initRoutes() {
      // přidání akce
      $this->addRoute('add', "add", 'add', "add/");
      // archiv akcí
      $this->addRoute('archive', "archiv", 'archive', "archiv/");
      // akce podle datumů
      $this->addRoute('normaldate',  "(?:(?P<day>[0-3]?[0-9]{1})/(?P<month>[0-1]?[0-9]{1})/(?P<year>[0-9]{4}))?", 'main','{day}/{month}/{year}/');
      // editace akce
      $this->addRoute('edit', "::urlkey::/edit", 'edit','{urlkey}/edit/');
      // detail akce
      $this->addRoute('detail', "::urlkey::", 'show','{urlkey}/');
      $this->addRoute('detailpdf', "::urlkey::.pdf", 'showPdf','{urlkey}.pdf');
      // exporty
      $this->addRoute('export', "(?P<type>(?:rss)|(?:atom)).xml", 'export', self::FEED_FILE);
      // list akcí
      $this->addRoute('normal', null, 'main', null);
   }
}

?>