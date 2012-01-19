<?php
class Articles_Routes extends Routes {
   const URL_PARAM_SORT = 'sort';

   function initRoutes() {
      $this->addRoute('top', "top", 'top', 'top/');
      $this->addRoute('archive', "archive", 'archive', 'archive/');

      $this->addRoute('content', "content.html", 'content', 'content.html');

      
      $this->addRoute('add', "add", 'add', "add/");
      $this->addRoute('edit', "::urlkey::/edit", 'edit','{urlkey}/edit/');
      $this->addRoute('editPrivate', "::urlkey::/edit-private/", 'editPrivate','{urlkey}/edit-private/');
      $this->addRoute('edittext', "edit-text/", 'edittext','edit-text/');
      $this->addRoute('detail', "::urlkey::", 'show','{urlkey}/');
      // list starších článků v xml
      $this->addRoute('lastlist', "lastlist.(?P<output>(?:xml))", 'lastList', 'lastlist.{output}');
      $this->addRoute('current', "current.(?P<output>(?:xml))", 'currentArticle', 'current.{output}');
      // exporty
      $this->addRoute('detailExport', "::urlkey::\.(?P<output>(?:pdf)|(?:xml)|(?:html))", 'exportArticle','{urlkey}.{output}');
      // kontrola url klíče
      $this->addRoute('checkUrlkey', 'c-url.php', 'checkUrlkey', 'c-url.php', 'XHR_Respond_VVEAPI');
	}
}

?>
