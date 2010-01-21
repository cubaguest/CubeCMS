<?php
class Photogalerymed_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('add', "add", 'add', "add/");
      $this->addRoute('edittext', "::urlkey::/edittext", 'edit','{urlkey}/edittext/');
      $this->addRoute('editphoto', "::urlkey::/editphotos/editphoto-::id::", 'editphoto','{urlkey}/editphotos/editphoto-{id}/');
      $this->addRoute('editphotos', "::urlkey::/editphotos", 'editphotos','{urlkey}/editphotos/');

      $this->addRoute('detail', "::urlkey::", 'show','{urlkey}/');
      $this->addRoute('detailpdf', "::urlkey::.pdf", 'showPdf','{urlkey}.pdf');

      $this->addRoute('normal', null, 'main', null);
	}
}

?>