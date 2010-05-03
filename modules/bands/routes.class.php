<?php
class Bands_Routes extends Routes {
   function initRoutes() {
      $this->addRoute('add', "add", 'add', "add/");
      $this->addRoute('edit', "::urlkey::/edit", 'edit','{urlkey}/edit/');
      $this->addRoute('detail', "::urlkey::", 'show','{urlkey}/');
      // list starších článků v xml
//      $this->addRoute('lastlist', "lastlist.(?P<output>(?:xml))", 'lastList', 'lastlist.{output}');
//      $this->addRoute('current', "current.(?P<output>(?:xml))", 'currentArticle', 'current.{output}');
      // exporty
      $this->addRoute('detailExport', "::urlkey::\.(?P<output>(?:pdf)|(?:xml)|(?:html))", 'exportBand','{urlkey}.{output}');


      $this->addRoute('normal', null, 'main', null);
	}
}

?>