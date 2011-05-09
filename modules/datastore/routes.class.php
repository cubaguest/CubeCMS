<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class DataStore_Routes extends Routes {
	function initRoutes() {
       $this->addRoute('itemsList', "list.html", 'itemsList', 'list.phtml');
       $this->addRoute('uploadFile', "upload.php", 'uploadFile', 'upload.php', 'XHR_Respond_VVEAPI');
//       $this->addRoute('edit', "edit", 'edit', 'edit/');
//       $this->addRoute('editPrivate', "editprivate", 'editPrivate', 'editprivate/');
//       $this->addRoute('editpanel', "editpanel", 'editPanel', 'editpanel/');
//       $this->addRoute('detailExport', "text\.(?P<output>(?:pdf)|(?:xml)|(?:html))", 'exportText','text.{output}');
//       $this->addRoute('normal', null, 'main', null);
	}
}

?>