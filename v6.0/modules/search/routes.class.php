<?php
class Search_Routes extends Routes {
   function initRoutes() {
//      $this->addRoute('archive', "archive", 'archive', 'archive/');
      $this->addRoute('search_api', "search_api.(?P<type>(?:json)|(?:php))", 'search', 'search_api.{type}');
      $this->addRoute('editsapi', "editsapi", 'editsapi','editsapi/');
//      $this->addRoute('detail', "::urlkey::", 'show','{urlkey}/');
//
//      $this->addRoute('detailpdf', "::urlkey::.pdf", 'showPdf','{urlkey}.pdf');

      $this->addRoute('normal', null, 'main', null);
   }
}

?>