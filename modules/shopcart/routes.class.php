<?php
class ShopCart_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('order', "objednavka/", 'order', 'objednavka/');
      $this->addRoute('orderComplete', "hotovo/", 'orderComplete', 'hotovo/');
//      $this->addRoute('editPrivate', "editprivate", 'editPrivate', 'editprivate/');
//      $this->addRoute('editpanel', "editpanel", 'editPanel', 'editpanel/');
//      $this->addRoute('content', "content.html", 'content', 'content.phtml');
//      $this->addRoute('detailExport', "text\.(?P<output>(?:pdf)|(?:xml)|(?:html))", 'exportText','text.{output}');
	}
}

?>