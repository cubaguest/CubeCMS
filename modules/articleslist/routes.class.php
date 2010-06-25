<?php
class ArticlesList_Routes extends Routes {
   function initRoutes() {
      $this->addRoute('editText', "edittext", 'editText','edittext/');
      $this->addRoute('detail', "::urlkey::", 'show','{urlkey}/');
      // list starších článků v xml
      $this->addRoute('lastlist', "lastlist.(?P<output>(?:xml))", 'lastList', 'lastlist.{output}');
      $this->addRoute('current', "current.(?P<output>(?:xml))", 'currentArticle', 'current.{output}');

      // editace textu
      $this->addRoute('normal', null, 'main', null);
	}
}

?>