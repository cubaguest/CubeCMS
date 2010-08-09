<?php
class Actions_Routes extends Routes {
   const FEEDS = true; // podle tohohle se generují feedy
   const FEED_FILE = '{type}.xml';

   function initRoutes() {
      // přidání akce
      $this->addRoute('add', "add", 'add', "add/");
      // archiv akcí
      $this->addRoute('archive', "archive", 'archive', "archive/");
      // akce podle datumů
      $this->addRoute('normaldate',  "(?:(?P<day>[0-3]?[0-9]{1})/(?P<month>[0-1]?[0-9]{1})/(?P<year>[0-9]{4}))?", 'main','{day}/{month}/{year}/');
      // editace akce
      $this->addRoute('edit', "::urlkey::/edit", 'edit','{urlkey}/edit/');

      // editace akce
      $this->addRoute('editlabel', "editlabel", 'editLabel','editlabel/');
      // list nadcházejiících akcí v xml
      $this->addRoute('feturedlist', "featuredlist.(?P<output>(?:xml))", 'featuredList', 'featuredlist.{output}');
      // list s právě probíhající akcí
      $this->addRoute('current', "current.(?P<output>(?:xml))", 'currentAct', 'current.{output}');
      // detail akce
      $this->addRoute('detail', "::urlkey::", 'show','{urlkey}/');
      // exporty
      $this->addRoute('export', "(?P<type>(?:rss)|(?:atom)).xml", 'export', self::FEED_FILE);
      // export článku
      $this->addRoute('detailExport', "::urlkey::\.(?P<output>(?:pdf)|(?:xml))", 'showData','{urlkey}.{output}');
      // list akcí
      $this->addRoute('normal', null, 'main', null);
   }
}

?>