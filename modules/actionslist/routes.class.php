<?php
class ActionsList_Routes extends Actions_Routes {
   function initRoutes() {
      parent::initRoutes();
//      $this->addRoute('add', "add", 'add', "add/");
//      // archiv akcí
//      $this->addRoute('archive', "archiv", 'archive', "archiv/");
//      // akce podle datumů
//      $this->addRoute('normaldate',  "(?:(?P<day>[0-3]?[0-9]{1})/(?P<month>[0-1]?[0-9]{1})/(?P<year>[0-9]{4}))?", 'main','{day}/{month}/{year}/');
//      // seznam kategorií pro přejití
      $this->addRoute('listCatAdd', "listcat.phtml", 'listCatAdd', 'listcat.phtml');
//
//      $this->addRoute('feturedlist', "featuredlist.(?P<output>(?:xml))", 'featuredList', 'featuredlist.{output}');
//      // list s právě probíhající akcí
////      $this->addRoute('current', "current.(?P<output>(?:xml))", 'currentAct', 'current.{output}');
//      // detail akce
//      $this->addRoute('detail', "::urlkey::", 'show','{urlkey}/');
//      // exporty
//      $this->addRoute('export', "(?P<type>(?:rss)|(?:atom)).xml", 'export', self::FEED_FILE);
//
//      $this->addRoute('detailExport', "::urlkey::\.(?P<output>(?:pdf)|(?:xml))", 'non','{urlkey}.{output}');
//
//      // list akcí
//      $this->addRoute('normal', null, 'main', null);
   }
}

?>