<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Text_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('edit', "edit", 'edit', 'edit/');
      $this->addRoute('preview', "preview", 'preview', 'preview/');
      $this->addRoute('editPrivate', "editprivate", 'editPrivate', 'editprivate/');
      $this->addRoute('editpanel', "editpanel", 'editPanel', 'editpanel/');
      $this->addRoute('content', "content.html", 'content', 'content.phtml');
      $this->addRoute('detailExport', "text\.(?P<output>(?:pdf)|(?:xml)|(?:html))", 'exportText','text.{output}');
      $this->addRoute('normal', null, 'main', null);
	}
}

?>