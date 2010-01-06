<?php
class News_Routes extends Routes {
   function initRoutes() {
      $this->addRoute('archive', "archive", 'archive', 'archive/');
      $this->addRoute('add', "add", 'add', "add/");
      $this->addRoute('edit', "::urlkey::/edit", 'edit','{urlkey}/edit/');
      $this->addRoute('detail', "::urlkey::", 'show','{urlkey}/');

      $this->addRoute('detailpdf', "::urlkey::.pdf", 'showPdf','{urlkey}.pdf');

      $this->addRoute('normal', null, 'main', null);
   }
}

?>